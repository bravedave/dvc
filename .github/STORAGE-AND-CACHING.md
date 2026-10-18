# Storage and Caching

This document defines storage and caching expectations for DVC framework changes
and applications built on DVC. Implementation descriptions refer to this source
checkout, not necessarily the version installed in an application's Composer
dependencies. Verify the installed implementation before relying on a guarantee.

## Storage Boundaries

- SQL records and persistent documents are authoritative. Cached values are
  disposable and must never be the only copy of business data.
- `config::dataPath()` normally resolves the application's `data` directory. A
  `data/datapath` redirect can select an existing writable directory. Applications
  can override storage configuration; verify the effective path rather than
  assuming a particular deployment layout.
- Keep private documents, credentials, and cache-health markers outside public
  web roots. Resolve document access through trusted application storage methods
  and enforce authorization before returning content.
- Separate unrelated datasets, tenants, environments, and test fixtures in both
  SQL and filesystem storage. A database setting does not automatically establish
  the correct document store or an authorization boundary.
- Back up authoritative SQL and documents according to application requirements.
  Cache persistence is not a substitute for database durability or backups.

Source: [Framework configuration](../src/bravedave/dvc/config.php).

## Backend Policy

DVC defaults `DB_CACHE` to an empty string, disabling caching. Applications must
choose an appropriate backend explicitly; framework support does not imply that
a backend is suitable for every execution model.

| Backend | Appropriate Use | Boundary |
| --- | --- | --- |
| Disabled | Workloads without a reviewed cache policy; isolated tests; application-defined permanent disablement | Reads miss and mutations are no-ops, including on retained cache objects |
| APCu (`APC`) | Explicitly selected workloads whose readers and writers share a verified coherent APCu domain | Independent CLI processes cannot invalidate PHP-FPM's local APCu entries |
| Redis (`REDIS`) | Record caches shared by web workers, CLI processes, or background jobs | All participants need the same endpoint, key identity, and invalidation policy |

Use Redis for record caching that must remain coherent across independent web
and CLI processes. Do not silently replace Redis with APCu during an outage.
APCu remains explicitly supported by DVC, but is not a shared-cache substitute.
TTL bounds the lifetime of stale entries; it does not establish consistency.

Redis hostnames and Unix socket paths are supported. Deployment owners must
verify connectivity and service-user permissions for the chosen transport.
The current adapter does not configure authentication, TLS, or logical database
selection; do not assume these controls are supplied by framework configuration.
Keep Redis on a trusted private network or a suitably restricted local socket.

## Cache Identity

Ordinary DAO keys include the database cache prefix, table, record ID, and
optional field. The default MySQL prefix derives from database host and name;
`DB_CACHE_PREFIX` can override it. Prefixes are namespacing conventions, not
collision-proof tenant isolation or authorization controls.

- Do not reuse a manual prefix across unrelated datasets.
- Custom keys must preserve dataset separation, including matching invalidation
  keys. Prefer the owning DAO's prefix/key methods over a parallel convention.
- Changes to key construction must cover reads, writes, and invalidation together.
- Client-facing or snapshot applications must explicitly enforce their own cache
  policy. DVC does not infer those roles from an application hostname or dataset.
- Authorize access independently of cache hits and misses. Sharing a cache must
  not grant access to records a caller could not otherwise retrieve.

Sources: [Database prefixes](../src/bravedave/dvc/db.php),
[DAO keys and invalidation](../src/bravedave/dvc/dao.php).

## Shared Redis Health

Applications opt into the shared cache-disable semaphore by setting
`DB_CACHE_SEMAPHORE_PATH` to an existing private directory shared by every cache
reader and writer. An empty path leaves Redis in legacy mode, without shared
outage coordination. APCu does not use this semaphore.

| Configuration | Default | Purpose |
| --- | --- | --- |
| `DB_CACHE` | `''` | Disabled, `APC`, or `REDIS` |
| `DB_CACHE_REDIS_SERVER` | `'redis'` | Redis hostname or Unix socket path |
| `DB_CACHE_PREFIX` | `''` | Optional application-defined database cache prefix |
| `DB_CACHE_TTL` | `600` | Default entry lifetime in seconds |
| `DB_CACHE_SEMAPHORE_PATH` | `''` | Shared private marker directory; enables the Redis health guard |
| `DB_CACHE_MAX_TTL` | `86400` | Positive upper bound for guarded entry TTLs and the recovery cooldown |
| `DB_CACHE_PROBE_INTERVAL` | `5` | Minimum interval in seconds between disabled-state health probes |

The semaphore controls are PHP configuration properties. Do not assume that
arbitrary similarly named fields in `defaults.json` are loaded automatically.

### Coordination Storage

The guard maintains one JSON marker per database host/name, configured cache
prefix, and Redis endpoint. The marker filename hashes that identity; it contains
health timestamps and a namespace generation, not business records or credentials.
Exclusive `flock()` locks coordinate state transitions and backend operations.

All participants must access the same physical files with reliable read/write
permissions. A container-local directory is insufficient for a multi-container
deployment. Verify shared locking behavior on the chosen filesystem, and keep
application clocks synchronized for cooldown timing.

Unavailable or malformed state causes the affected caller to bypass caching and
log the storage failure. A process unable to write coordination state cannot
notify other processes of its failure. Uniform service-user access and reliable
shared storage are therefore deployment prerequisites, not optional safeguards.

### Failure and Recovery

1. A missing Redis extension, connection failure, or detected cache-operation
   failure disables the shared cache identity. Failed invalidation also disables
   caching because the SQL write may already have succeeded.
2. Disabled callers bypass normal cache operations and continue through their
   SQL fallback. Retained cache objects honor the same guard. No APCu fallback
   is selected.
3. Calls while disabled trigger rate-limited health probes. Probing is
   demand-driven, not a separate background service. Guarded Redis connection
   and read timeouts are one second.
4. The first successful probe starts a full `DB_CACHE_MAX_TTL` cooldown. Renewed
   failure resets recovery; elapsed time alone cannot re-enable caching.
5. A successful probe after the cooldown permits normal operations. Namespace
   generations rotate across outage/recovery, making previous entries unreachable.
   Recovery does not flush the Redis database.

Guarded writes cap entry lifetimes to `DB_CACHE_MAX_TTL` and require a positive
TTL. Use consistent settings across participants. Reads capture a generation so
fills started before an outage cannot populate the recovered namespace. Custom
cache-fill code must read/check the guard before querying SQL; long-running
callers should perform a fresh cache read before filling after recovery.

Setting `DB_CACHE` to an empty string takes precedence over recovery. The guard
does not change an application's configured backend or permanent-disable policy.
Health transitions are logged without cached payloads. Account for increased SQL
load while caching is bypassed.

Source: [Cache implementation](../src/bravedave/dvc/cache.php).

## Mutation and Flush

Prefer DAO mutation methods so affected record and field caches are invalidated.
`UpdateByID()` invalidates after the SQL update returns. Direct SQL, imports,
external writers, and singleton/custom cache keys require their own coordinated
invalidation; inherited record-ID invalidation cannot discover arbitrary keys.

After-statement invalidation is not automatically after-commit invalidation in
an explicit transaction. Applications must coordinate invalidation with commit
when using transactions. A delayed reader can also cache an old SQL result after
ordinary update invalidation. The outage semaphore does not solve that normal
concurrency race; stronger guarantees require version checks or coordinated fills.

Guarded Redis wildcard deletion and `flush()` affect only the current identity's
active generation. Legacy Redis mode calls `flushDB()`, clearing the entire
selected Redis database, regardless of key prefixes. Do not share that legacy
flush domain with sessions, queues, or other data that must survive a cache flush.
Never use a whole-database flush as automatic outage recovery.

Dataset restores and external database replacement require a reviewed cache
invalidation plan. Coordinate authoritative data replacement and health-marker
handling; do not independently restore an older marker or blindly reuse old keys.

## Validation and Operations

Before enabling or changing caching:

1. Verify effective database identity, storage paths, PHP SAPI, backend, Redis
   endpoint, prefix, semaphore directory, TTL bound, and probe interval for every
   web and CLI entry point. Do not publish credentials or business records.
2. Test two datasets sharing a backend and prove that values and invalidation do
   not cross identities. Verify custom keys and field caches, not just DTO reads.
3. Exercise disabled caching on retained objects, then verify web-read,
   CLI-DAO-write, web-read freshness with caching enabled.
4. Test connection/operation failures, failed invalidation, missing extensions,
   unavailable/corrupt coordination state, repeated outages, cooldown, and delayed
   fills across separate processes. Confirm permanent disablement still wins.
5. Verify flush scope and authorization independently of cache behavior. Keep
   restore, flush, permission, and service-management operations explicitly scoped
   and supervised; documentation review is not authorization to perform them.

Run `php -n tests/dvc/CacheTest.php` for the isolated regression harness. It uses
temporary files, simulated time, child processes, and in-memory adapters; it does
not access a live Redis server or application database. Those tests establish
source-level behavior, not production permissions, filesystem locking, Redis
connectivity, or web/CLI deployment parity.

Sources: [Cache regression tests](../tests/dvc/CacheTest.php),
[Framework setup and cache configuration](../README.md#shared-redis-cache-health).
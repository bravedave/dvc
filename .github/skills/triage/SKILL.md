---
name: triage
description: 'Convert issue plaintext to BDD markdown, enrich Developer Notes with discovered implementation intent, and emit a Next Step confidence gate for issue-to-delta.'
---

# Triage

## Current Role

You are an expert DVC framework triage assistant.
You convert issue text to a clean BDD-style markdown issue file, then enrich developer-facing context so
project leads can quickly verify module scope, framework fit, and readiness for delta planning.

This skill supersedes and incorporates the behavior of `convert-plaintext-to-md` for issue triage use.

## Purpose

Given an issue source file such as `.issue/12345` or `.issue/12345.md`, produce or update the markdown
issue document so it is ready for the next workflow step:

- Markdown-normalized and readable
- Behavior/spec intent preserved (BDD-style)
- Developer Notes enriched with implementation targeting evidence
- A final `## Next Step` section with an explicit confidence score for running `issue-to-delta`
- A refreshed `summary.md` in the same folder, sorted by confidence (highest first)

## Usage

```bash
/triage <#file:{{issue-file}}> [rerun] [finalize] [guide #file:{{reference-file}}] [instructions] [platform={{name}}] [pre=<name>]
```

### Arguments

- `#file:{{issue-file}}` (required): Source issue file to process.
- `rerun`: Re-read existing markdown issue and update Developer Notes and Next Step confidence based on
  newly clarified details.
- `finalize`: Tighten formatting and wording, remove obvious placeholders, and output a handoff-ready
  issue file.
- `guide #file:{{reference-file}}`: Optional style guide file for formatting alignment.
- `instructions`: Additional user instructions for this run.
- `platform={{name}}`: Markdown renderer target (`GitHub` default).
- `pre=<name>`: Optional predefined formatting transform, same behavior as convert-plaintext-to-md.
- `defer-summary`: Optional batch optimization flag. When present, skip per-file summary rebuild for this run.

## File Handling Rules

1. If input is plaintext and matching `.md` does not exist, create `<file>.md` in the same directory and
   convert into it.
2. If matching `.md` exists, treat that markdown file as the active working document.
3. Preserve source intent. Do not drop meaningful details.
4. After each triage run, recreate `summary.md` in the same directory as the triaged file.
5. Summary input files are issue files matching `^[0-9]+\.md$` only (non-recursive, current directory only).
6. Exclude all non-numeric markdown files (for example `delta.12345.md`, `summary.md`, `triage.md`).
7. If `defer-summary` is set, do not rebuild `summary.md` for that individual run.

## Chat Title

For every triage run, include this exact line in the first assistant response:

`Suggested chat title: Triage [filename]`

Where `[filename]` is the basename of the file being triaged (include extension if present).

This skill does not assume the agent can programmatically rename the chat UI. The required, enforceable
behavior is to emit the suggested title text for the user or host environment to apply.

Examples:
- `.issue/18482` -> `Suggested chat title: Triage 18482`
- `.issue/18482.md` -> `Suggested chat title: Triage 18482.md`

## Core Workflow

1. Normalize markdown structure from source content.
2. Detect BDD signals and promote them into clear headings and lists.
3. Ensure or create `## Developer Notes`.
4. Enrich Developer Notes with discovered implementation intent evidence (see required items below).
5. Ensure or create `## Next Step` with confidence score.
6. On `rerun`, update confidence score and rationale based on current note quality.
7. Recreate `summary.md` from all numeric issue files in the current folder (unless `defer-summary` is set).

## Batch Optimization

Use `defer-summary` only in orchestrated batch workflows.

- Recommended pattern for batches:
  1. Run triage on each target file with `defer-summary`.
  2. Rebuild `summary.md` once at the end.
- Integrity fallback:
  - If a batch run aborts or partially fails, still perform one final summary rebuild before exit/report.
- Single-file runs:
  - Do not use `defer-summary`; rebuild summary immediately as normal.

## Summary Maintenance

After each run (including `rerun` and `finalize`), regenerate `summary.md` in the same folder as the triaged issue, except when `defer-summary` is explicitly set.

### Summary Scope

- Use only files in the current folder (no recursion).
- Include only filenames matching `^[0-9]+\.md$`.
- Exclude files such as `delta.12345.md`, `summary.md`, and any other non-numeric markdown names.

### Summary Table Requirements

`summary.md` must contain:

- H1: `# Issue Triage Confidence Summary`
- Metadata bullets for scope and generated timestamp
- Table columns: `Issue | Title | Confidence`

Populate values as follows:

- `Issue`: markdown link to forum item using issue id from filename.
  - Format: `[12345.md](https://cmss.darcy.com.au/forum/view/12345)`
  - Rule: id is the numeric filename stem.
- `Title`: level-1 heading (`# ...`) from the issue file; if missing, use `(missing H1)`
- `Confidence`: parsed from `## Next Step` line (`confidence NN%`); if missing, use `n/a`

### Sort Order

- Sort rows by numeric confidence descending (highest first).
- Rows with `n/a` confidence appear at the bottom.

### Reference Shell Snippet

Use this as a reference implementation when regenerating `summary.md`:

```bash
{
  echo '# Issue Triage Confidence Summary'
  echo
  echo '- scope: current folder issue files only (non-recursive, numeric *.md)'
  echo "- generated: $(date '+%Y-%m-%d %H:%M:%S %Z')"
  echo
  echo '| Issue | Title | Confidence |'
  echo '|---|---|---:|'

  tmp=$(mktemp)
  for f in [0-9]*.md; do
    title=$(rg -m1 '^# ' "$f" | sed 's/^# //')
    [ -z "$title" ] && title='(missing H1)'
    title=${title//|/\\|}

    score=$(rg -o 'confidence [0-9]+%' "$f" | head -n1 | rg -o '[0-9]+' || true)
    if [ -z "$score" ]; then
      score='-1'
      score_display='n/a'
    else
      score_display="${score}%"
    fi

    id="${f%.md}"
    issue_link="[$f](./$f)"  # Local reference; customize URL for your issue tracking system

    printf '%s|%s|%s|%s\n' "$score" "$issue_link" "$title" "$score_display" >> "$tmp"
  done

  sort -t'|' -k1,1nr "$tmp" | while IFS='|' read -r score issue_link title score_display; do
    echo "| $issue_link | $title | $score_display |"
  done

  rm -f "$tmp"
} > summary.md
```

## BDD Conversion Rules

When the issue describes behavior, incidents, or expected outcomes:

- Promote labels like `Steps to Reproduce`, `Expected Result`, `Actual Result`, `Notes`, `Given/When/Then`
  to headings.
- Keep reproduction flows as ordered lists.
- Keep expected/actual observations as bullet lists.
- Preserve user story and acceptance criteria phrasing.
- Keep author/date metadata directly beneath title as short bullets.

## Developer Notes Requirements

The `## Developer Notes` section must include the following minimum items (add or update on each run):

- `target module:`
  - Explicit module path(s). If ambiguous, state `ambiguous` and explain why.
- `intent:`
  - Scope lock statement, for example: `extend module, do not infer new module`.
- `discovered framework artifacts:`
  - 3 to 4 concrete bullets that identify likely files, classes, methods, routes, or views involved.
  - Use best-effort discovery from issue text and references.
- `implementation shape:`
  - 1 to 3 bullets that describe where behavior is likely implemented (UI/view, controller, handler, DAO,
    config, api doc).
- `constraints and risks:`
  - 1 to 3 bullets capturing access/security constraints, data sensitivity, or uncertainty.

### Artifact Discovery Quality Bar

For `discovered framework artifacts`, prioritize high-signal references such as:

- Module-local `controller.php` and `handler.php` methods
- Specific view files and approximate UI location hints
- DAO/DTO/schema files when data behavior is implicated
- Existing constants or config values that drive branching behavior

Avoid generic statements that are not actionable.

## Next Step Section

Always append or update this section at the end of the document:

```markdown
## Next Step
- Run the `issue-to-delta` skill, confidence NN%
- confidence rationale:
  - <short reason 1>
  - <short reason 2>
```

### Confidence Scoring Guidance

Set `NN` as an integer from 0 to 100 using this guidance:

- 80-100: Target module clear, intent scoped, artifacts concrete, blockers minimal.
- 60-79: Mostly clear but one or two important unknowns remain.
- 40-59: Partial targeting, significant ambiguity in module/artifacts.
- 0-39: Major ambiguity; not ready for reliable delta planning.

On `rerun`, recompute and update confidence based on newly resolved or newly discovered ambiguity.
Do not duplicate the `## Next Step` section; maintain a single current section.

## Ambiguity Handling

If module target is unclear:

- Keep conversion complete anyway.
- Mark `target module: ambiguous`.
- Add a concise clarification question in Developer Notes.
- Lower confidence score accordingly.

## Safety and Scope

- Do not modify application source code while running this skill.
- Only create or update markdown files in the triage folder: the active issue file and `summary.md`.
- Preserve all meaningful technical details from input.
- Prefer DVC framework terminology and module conventions.

## Success Criteria

100% success means the issue markdown is ready, or very close to ready, for `issue-to-delta`:

- Original issue intent intact
- Markdown is clean and scannable
- Developer Notes demonstrate concrete targeting intent
- Next Step confidence gate is present and credible

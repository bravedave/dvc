# Implement Delta Change

You are implementing a change described in a delta file. Follow these steps carefully.

## Instructions

### 1. Read the Selected Delta Text
The user has selected text from a `delta.md` file that describes the change to implement. This selection contains:
- The feature or change description
- Acceptance criteria
- Implementation notes or constraints

Parse this carefully to understand what needs to be built.

### 2. Check for Context READMEs

**Current Folder README:**
Before implementing, check if a `README.md` exists in the same folder as the delta file or the target module folder. This README may contain:
- Project-specific setup instructions
- Development environment notes (e.g., server already running)
- Local conventions or constraints

**Root README:**
Also check the workspace root `README.md` for:
- Overall project architecture
- Development environment setup (dev containers, servers, URLs)
- Important constraints (e.g., "server already running at https://app.localhost/")

### 3. Implement the Change

With context gathered:
1. Follow the patterns in `.github/copilot-instructions.md` for DVC framework modules
2. Respect any environment notes from READMEs (e.g., don't start servers if already running)
3. Create/modify files according to the module structure
4. Update the delta.md to mark completed items

## Key Reminders

- **Check READMEs first** - They may contain critical environment info
- **Don't start dev servers** if README indicates one is already running
- **Follow DVC patterns** - Use the copilot-instructions.md as the framework guide
- **Mark progress** - Update delta.md as items are completed

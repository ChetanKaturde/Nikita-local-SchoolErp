# Local Development Setup - test-L Branch

## Branch Configuration

You are now on the `local-test-L` branch which tracks the remote `test-L` branch from ChetanKaturde repository.

### Current Setup
```
Remote: origin = https://github.com/ChetanKaturde/Nikita-local-SchoolErp.git
Local Branch: local-test-L
Remote Branch: origin/test-L
```

---

## Protected Local Files (Not Synced to GitHub)

The following files are **ignored by .gitignore** and will NOT be affected by git operations:

### Environment Files
- `.env` - Your local database and app configuration
- `.env.backup`
- `.env.local`
- `.env.production`

### Dependencies & Build
- `/vendor` - Composer dependencies
- `/node_modules` - NPM dependencies
- `/public/build`
- `/public/hot`
- `/public/storage`

### IDE & Editor
- `.vscode/`
- `.idea/`
- `.phpactor.json`

### Logs & Cache
- `*.log`
- `.phpunit.result.cache`
- `.phpunit.cache/`

### Storage
- `/storage/app/*` (except `.gitignore`)
- `/storage/*.key`
- `/storage/pail`

---

## How to Update Code from test-L Branch

### Option 1: Pull Latest Changes (Safe for .env)
```bash
git checkout local-test-L
git pull origin test-L
```

This will **only update code files**, your `.env` and ignored files remain unchanged.

### Option 2: Force Update Code (If Conflicts Occur)
```bash
git checkout local-test-L
git fetch origin test-L
git reset --hard origin/test-L
```
⚠️ **Warning:** This will discard any local code changes, but `.env` remains safe!

---

## How to Keep Your Local Changes

### If You Have Local Code Modifications

**Option A: Stash Them Temporarily**
```bash
# Save your changes
git stash push -m "my-local-changes"

# Pull updates
git pull origin test-L

# Re-apply your changes (may need to resolve conflicts)
git stash pop
```

**Option B: Create a Personal Branch**
```bash
# Create your own branch from test-L
git checkout -b my-local-customizations

# Commit your changes
git add .
git commit -m "My local customizations"
```

---

## Current Stashed Changes

You have stashed changes from your previous work:
```
stash@{0}: local-changes-before-test-L
```

### To Restore Stashed Changes
```bash
# Apply without removing from stash
git stash apply stash@{0}

# OR apply and remove from stash
git stash pop stash@{0}
```

### To Apply to This Branch
```bash
git stash pop stash@{0}
```

---

## Verify Protected Files

Check that `.env` is safe:
```bash
# Should show .env as "untracked" (ignored)
git status

# Should NOT appear in git diff
git diff HEAD
```

---

## Workflow Summary

### Daily Workflow
1. **Start Work:**
   ```bash
   git checkout local-test-L
   git pull origin test-L
   ```

2. **Make Changes:** (your `.env` stays intact)

3. **Don't Commit Sensitive Data:**
   - `.env` is automatically ignored
   - But double-check before `git push`

### Updating from Remote
```bash
git checkout local-test-L
git fetch origin
git pull origin test-L
```

### If You Want to Keep Local Customizations
1. Create a separate branch for your changes
2. Or use git stash to save them
3. Or cherry-pick specific commits

---

## Quick Commands Reference

| Command | Purpose |
|---------|---------|
| `git status` | Check current state |
| `git pull origin test-L` | Update from remote |
| `git stash push -m "note"` | Save local changes |
| `git stash pop` | Restore saved changes |
| `git stash list` | List all stashes |
| `git checkout local-test-L` | Switch to test-L branch |
| `git log --oneline -5` | View recent commits |

---

## Important Notes

✅ **SAFE Files** (Won't be affected):
- `.env` and variants
- `/vendor`
- `/node_modules`
- `/storage/app`
- IDE settings

⚠️ **CAUTION Files** (Will be updated):
- Code files (`.php`, `.js`, `.css`)
- Views (`.blade.php`)
- Config files (not `.env`)
- Migrations

❌ **DON'T Commit**:
- API keys
- Database passwords
- Personal configurations

---

## Troubleshooting

### "Your local changes would be overwritten"
```bash
# Stash your changes
git stash push -m "backup"

# Pull updates
git pull origin test-L

# Try to re-apply
git stash pop
```

### "Merge conflicts"
```bash
# See conflicts
git status

# Edit files to resolve conflicts

# Mark as resolved
git add <filename>

# Complete merge
git commit
```

### "Accidentally committed .env"
```bash
# Remove from git history (but keep locally)
git rm --cached .env
git commit -m "Remove .env from tracking"
```

---

## Current Branch Info

```bash
# Check which branch you're on
git branch --show-current

# Check remote URL
git remote -v

# Check if up to date
git status
```

---

**Setup Date:** 2026-03-24
**Branch:** local-test-L (tracking origin/test-L)
**Repository:** https://github.com/ChetanKaturde/Nikita-local-SchoolErp.git

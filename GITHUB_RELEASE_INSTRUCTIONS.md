# Step 1: Create GitHub Release for v2.1.0

## 📤 Manual Steps to Create Release on GitHub

### Option A: Via GitHub Web UI (Recommended for visibility)

1. **Go to Releases Page**
   ```
   https://github.com/coijiryuna/simbaapi/releases
   ```

2. **Click "Create a new release"**

3. **Fill in Release Details**
   - **Tag version**: `v2.1.0` (should already be listed as existing tag)
   - **Release title**: `SIMBA API v2.1.0 - Laravel Integration Release`
   - **Describe this release**: Copy content from `GITHUB_RELEASE_BODY.md` (see below)
   - **This is a pre-release**: ☐ Unchecked (it's a stable release)
   - **Latest release**: ☑ Checked

4. **Paste Release Content**
   - Copy the content from `GITHUB_RELEASE_BODY.md`
   - Paste into the description box
   - Optionally add any binary artifacts if needed

5. **Click "Publish release"**

### Option B: Via GitHub CLI (if installed)

```bash
# Requires: gh CLI installed and authenticated
gh release create v2.1.0 \
  --title "SIMBA API v2.1.0 - Laravel Integration Release" \
  --notes "$(cat GITHUB_RELEASE_BODY.md)"
```

### Option C: Via curl API (advanced)

```bash
# First, get your GitHub token from: https://github.com/settings/tokens
# Create Personal Access Token with 'repo' scope

curl -X POST \
  https://api.github.com/repos/coijiryuna/simbaapi/releases \
  -H "Authorization: token YOUR_GITHUB_TOKEN" \
  -H "Content-Type: application/json" \
  -d @- << 'EOF'
{
  "tag_name": "v2.1.0",
  "name": "SIMBA API v2.1.0 - Laravel Integration Release",
  "body": "$(cat GITHUB_RELEASE_BODY.md)",
  "draft": false,
  "prerelease": false
}
EOF
```

---

## ✅ What Gets Created

Once you publish the release:

1. ✅ **Release Page** appears at: `github.com/coijiryuna/simbaapi/releases/tag/v2.1.0`
2. ✅ **Automatically linked** to the git tag
3. ✅ **Notification sent** to watchers
4. ✅ **Available for download** (generates source archives)
5. ✅ **Indexed by Packagist** (if auto-sync enabled)

---

## 📋 Release Content Checklist

The release will include:

- [x] Full feature list and highlights
- [x] Installation instructions for both Laravel and CodeIgniter 4
- [x] Link to full CHANGELOG
- [x] Migration guide (no breaking changes)
- [x] FAQ section
- [x] Testing verification status
- [x] Comparison link to v2.0.0

---

## 🔗 Quick Links for Release

| Resource | URL |
|----------|-----|
| Release Body | `GITHUB_RELEASE_BODY.md` (in this repo) |
| Releases Page | https://github.com/coijiryuna/simbaapi/releases |
| Compare v2.0→v2.1 | https://github.com/coijiryuna/simbaapi/compare/v2.0.0...v2.1.0 |
| CHANGELOG | https://github.com/coijiryuna/simbaapi/blob/main/CHANGELOG.md |

---

## 💡 Pro Tips

- Release body is **markdown-formatted** — links work automatically
- Use `[text](url)` for manual linking
- Mention contributors: `@username`
- Use emojis (✅, ✨, 🚀, etc.) for visual appeal
- Check preview before publishing

---

## ✨ After Release Creation

Once release is published:

1. ✅ GitHub automatically creates downloadable source archives
2. ✅ Release appears in "Latest release" badge
3. ✅ NPM/Packagist auto-sync triggers (if configured)
4. ✅ Release is indexed for search
5. ✅ Commit/tag link from releases page visible

See: [Creating Releases - GitHub Docs](https://docs.github.com/en/repositories/releasing-projects-on-github/managing-releases-in-a-repository)

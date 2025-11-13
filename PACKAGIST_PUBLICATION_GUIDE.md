# Step 2: Publish to Packagist

## 📦 What is Packagist?

Packagist is the default Composer package repository. Publishing here makes your package installable via:
```bash
composer require simba/api
```

---

## 🚀 Option A: Auto-Sync with GitHub (Recommended - Easiest)

If you've already set up Packagist auto-sync from GitHub, it will automatically:

1. **Detect the v2.1.0 tag** on GitHub
2. **Create a package release** automatically
3. **Index the release** within minutes
4. **Make it available** via Composer

### Status Check:
Go to: `https://packagist.org/packages/simba/api`

Look for:
- ✅ Package listed
- ✅ v2.1.0 in version list
- ✅ Installation instructions available

**No action needed** if auto-sync is enabled!

---

## 🔧 Option B: Manual Setup (First Time or If Auto-Sync Disabled)

### Step 1: Verify GitHub Hook

1. Go to your GitHub repo: https://github.com/coijiryuna/simbaapi/settings/hooks
2. Look for **"Packagist" webhook**
3. If **NOT present**, skip to Step 2
4. If **present**, auto-sync is enabled ✅

### Step 2: Register on Packagist (if not already)

1. Visit: https://packagist.org/register
2. Sign up with GitHub account (recommended) or email
3. Verify email

### Step 3: Submit Package

1. Go to: https://packagist.org/packages/submit
2. Enter your repository URL:
   ```
   https://github.com/coijiryuna/simbaapi
   ```
3. Click **"Check"**
4. If valid, click **"Submit"**

### Step 4: Set Up GitHub Hook (Auto-Sync for Future Releases)

1. Packagist will show instructions to add GitHub webhook
2. Copy the **webhook URL** from Packagist
3. Go to GitHub repo settings → Webhooks (or use `Settings > Webhooks & Services`)
4. Add new webhook:
   - **Payload URL**: Paste Packagist webhook URL
   - **Content type**: `application/json`
   - **Events**: "Push events" + "Tag push events"
5. Click **"Add webhook"**

Now all future tags will auto-sync! ✅

### Step 5: Verify Installation

Test in a temporary directory:
```bash
mkdir test-install && cd test-install
composer require simba/api:^2.1
```

Should install successfully with v2.1.0 ✅

---

## ✅ Verification Checklist

After submitting to Packagist:

- [ ] Package shows at `packagist.org/packages/simba/api`
- [ ] v2.1.0 appears in version list
- [ ] Installation instructions display correctly
- [ ] README shows on package page
- [ ] Composer can find and install it
- [ ] GitHub webhook configured (for auto-sync)

---

## 🔗 Important URLs

| Resource | URL |
|----------|-----|
| Package Page | https://packagist.org/packages/simba/api |
| Composer Registry | https://packagist.org |
| Composer Docs | https://getcomposer.org/doc |
| GitHub Webhook Settings | https://github.com/coijiryuna/simbaapi/settings/hooks |

---

## 💡 Post-Publication Checks

### Check 1: Composer Search
```bash
composer search simba api
```

You should see:
```
simba/api Laravel & CodeIgniter 4 SIMBA API Integration Package
```

### Check 2: Package Info
```bash
composer info simba/api
```

Should show v2.1.0 as available version

### Check 3: Direct Installation
```bash
composer require simba/api:2.1.0
```

Should install from Packagist automatically

### Check 4: Package Stats
Visit: https://packagist.org/packages/simba/api
- Check download stats
- Verify latest version
- Confirm all versions listed

---

## 🎁 What Happens After Publication

Once on Packagist:

1. ✅ **Searchable** — Appears in Composer search
2. ✅ **Installable** — `composer require simba/api` works
3. ✅ **Discoverable** — Listed in Packagist directory
4. ✅ **Updatable** — Composer can detect new versions
5. ✅ **Statsable** — Download stats tracked
6. ✅ **Documented** — README displays on package page

---

## ❓ Troubleshooting

### Package Not Showing Up
- **Wait 5-10 minutes** for indexing
- Check package URL: `packagist.org/packages/simba/api`
- Try manual submission again if needed

### v2.1.0 Not Available
- **Verify tag exists** on GitHub: `git tag -l v2.1.0`
- **Push tags** if needed: `git push origin --tags`
- **Wait** for webhook to trigger (auto-sync)
- Check webhook delivery in GitHub settings

### Installation Fails
- **Check PHP version** requirement in composer.json
- **Verify dependencies** are available
- **Clear Composer cache**: `composer clear-cache`
- **Try** `composer install --no-cache`

### Webhook Not Triggering
- Verify webhook URL in Packagist is correct
- Check GitHub webhook delivery logs (Settings > Webhooks)
- Re-add webhook if needed
- Test webhook delivery manually

---

## 📚 Additional Resources

- [Packagist Documentation](https://packagist.org/about)
- [Submitting Packages](https://packagist.org/about#how-to-submit)
- [Composer Documentation](https://getcomposer.org/doc/)
- [GitHub Webhooks](https://docs.github.com/en/developers/webhooks-and-events/webhooks)

---

## ✨ After Everything

Once published to Packagist:

1. **Users can install** with: `composer require simba/api`
2. **Version control** automatic via Composer
3. **Updates** easy to push (just tag in git)
4. **Statistics** tracked on Packagist
5. **Community** can discover your package

**Package is now production-ready for all PHP developers!** 🎉

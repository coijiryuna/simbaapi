# ⚡ QUICK START - Release Steps 1 & 2

## 🎯 TL;DR

Your package v2.1.0 is ready. Execute two steps:

### **Step 1: Create GitHub Release**

**Pick ONE method:**

#### Method A: Web UI (Easiest) 🌐
1. Go: https://github.com/coijiryuna/simbaapi/releases
2. Click: "Create a new release"
3. Tag: `v2.1.0`
4. Title: `SIMBA API v2.1.0 - Laravel Integration Release`
5. Description: Copy-paste content from `GITHUB_RELEASE_BODY.md`
6. Click: "Publish release" ✅

#### Method B: CLI (Fast) ⚡
```bash
gh release create v2.1.0 \
  --title "SIMBA API v2.1.0 - Laravel Integration Release" \
  --notes "$(cat GITHUB_RELEASE_BODY.md)"
```

#### Method C: API (Code) 🔧
See `GITHUB_RELEASE_INSTRUCTIONS.md` (curl command included)

**Verify:**
```
Visit: https://github.com/coijiryuna/simbaapi/releases/tag/v2.1.0
✅ Should see your release!
```

---

### **Step 2: Publish to Packagist**

**Pick ONE method:**

#### Method A: Auto-Sync (Easiest) ✨
- Already set up on Packagist?
- GitHub webhook auto-triggers ✅
- Wait 5-10 minutes
- v2.1.0 auto-appears on Packagist
- **Done!**

#### Method B: Manual Register (First Time)
1. Go: https://packagist.org/register
2. Sign up (GitHub recommended)
3. Go: https://packagist.org/packages/submit
4. Enter: `https://github.com/coijiryuna/simbaapi`
5. Click: "Check" → "Submit"
6. Configure webhook (Packagist shows instructions)

#### Method C: Manual Update (If Exists)
1. Login to Packagist
2. Go: https://packagist.org/packages/simba/api
3. Click: "Update"
4. Wait 5-10 min
5. v2.1.0 appears ✅

**Verify:**
```bash
composer require simba/api:2.1.0
```
Should install successfully ✅

---

## 📍 Status

| Item | Status |
|------|--------|
| Code | ✅ Ready |
| Tests | ✅ Passing |
| Docs | ✅ Complete |
| Git Tag | ✅ v2.1.0 pushed |
| GitHub Release | ⏳ **ACTION NEEDED** |
| Packagist | ⏳ **ACTION NEEDED** |

---

## 🔗 Key Links

- **Repo**: https://github.com/coijiryuna/simbaapi
- **Releases**: https://github.com/coijiryuna/simbaapi/releases
- **Packagist**: https://packagist.org/packages/simba/api

---

## 📚 Full Guides

- **GitHub Release Details**: `GITHUB_RELEASE_INSTRUCTIONS.md`
- **Packagist Setup Guide**: `PACKAGIST_PUBLICATION_GUIDE.md`
- **Combined Steps**: `RELEASE_STEPS_1_AND_2.md`
- **Full Checklist**: `RELEASE_MASTER_CHECKLIST.md`

---

## ✅ Success = Both Steps Done

**GitHub Release** + **Packagist Published** = Package Available to All PHP Developers 🎉

---

**Next**: Do Step 1, then Step 2. That's it!

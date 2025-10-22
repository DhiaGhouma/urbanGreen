# 🎉 Using GitHub Container Registry (GHCR)

## ✅ What Changed?

Instead of Docker Hub, we now use **GitHub Container Registry (GHCR)** which:

-   ✅ **NO secrets needed** - Uses automatic `GITHUB_TOKEN`
-   ✅ **Works for collaborators** - No special permissions needed
-   ✅ **Free and unlimited** - For public repositories
-   ✅ **Integrated with GitHub** - View packages on GitHub directly
-   ✅ **No GitHub push protection issues** - Uses official GitHub tokens

---

## 🚀 How to Use Your Docker Images

### Pull from GHCR (instead of Docker Hub)

```bash
# Pull latest image
docker pull ghcr.io/dhiaghouma/urbangreen:latest

# Pull specific branch
docker pull ghcr.io/dhiaghouma/urbangreen:moetaz

# Pull specific commit
docker pull ghcr.io/dhiaghouma/urbangreen:moetaz-abc123
```

### Run the Container

```bash
# Run latest image
docker run -d -p 8080:80 ghcr.io/dhiaghouma/urbangreen:latest

# Or with docker-compose (auto-pulls from GHCR)
docker-compose up -d
```

---

## 📦 View Your Packages

After successful build, view your Docker images at:

-   https://github.com/DhiaGhouma?tab=packages
-   Or: https://github.com/DhiaGhouma/urbanGreen/pkgs/container/urbangreen

---

## 🔐 Pull Private Images (If Needed Later)

If the repository becomes private, authenticate once:

```bash
# Create a GitHub Personal Access Token with 'read:packages' permission
# Then login:
echo "YOUR_GITHUB_TOKEN" | docker login ghcr.io -u YOUR_GITHUB_USERNAME --password-stdin

# Then pull normally
docker pull ghcr.io/dhiaghouma/urbangreen:latest
```

---

## 📊 Comparison

| Feature                 | Docker Hub              | GHCR                          |
| ----------------------- | ----------------------- | ----------------------------- |
| **Secrets Needed**      | Yes (blocked by GitHub) | No ✅                         |
| **Collaborator Access** | Need settings access    | Works automatically ✅        |
| **Free Tier**           | Yes (with limits)       | Unlimited for public repos ✅ |
| **Integration**         | External                | Native GitHub ✅              |
| **Image URL**           | `docker.io/user/repo`   | `ghcr.io/owner/repo`          |

---

## ✨ Benefits

1. **Zero Configuration** - Just push and it works!
2. **No Secrets Management** - Uses automatic `GITHUB_TOKEN`
3. **Better Security** - No tokens in repository
4. **Native Integration** - View packages directly on GitHub
5. **Collaborator Friendly** - Works without special permissions

---

## 🎯 Your Images Are Now At

```
ghcr.io/dhiaghouma/urbangreen:latest
ghcr.io/dhiaghouma/urbangreen:moetaz
ghcr.io/dhiaghouma/urbangreen:moetaz-[commit-hash]
```

**No Docker Hub needed! Everything is on GitHub!** 🎊

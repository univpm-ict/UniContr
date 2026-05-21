Write-Host "Push monorepo su GitHub..."
git push origin main

Write-Host "Split e push backend su Bitbucket..."
git branch -D split-backend 2>$null
git subtree split --prefix=unicontract-backend -b split-backend
git push bitbucket-be split-backend:main
git branch -D split-backend

Write-Host "Split e push frontend su Bitbucket..."
git branch -D split-frontend 2>$null
git subtree split --prefix=unicontract-frontend -b split-frontend
git push bitbucket-fe split-frontend:main
git branch -D split-frontend

Write-Host "Fatto."
git fetch --all
git reset --hard origin/master
chmod -R 755 .
cp ../config.json ./src/config/config.json
gulp deploy

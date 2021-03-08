git fetch --all
git reset --hard origin/master
cp /var/www/netschool/config.json /var/www/netschool/www/src/config/config.json
gulp deploy

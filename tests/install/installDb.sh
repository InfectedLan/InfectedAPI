echo "create database $2" | mysql -h localhost -u travis
mysql -h localhost -u travis $2 < $1

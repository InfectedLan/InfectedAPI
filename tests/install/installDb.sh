echo "create database $2" | mysql -h localhost -u travis
mysql -h localhost -u root $2 < $1

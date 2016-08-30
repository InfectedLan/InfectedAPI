echo "create database $2" | mysql -h localhost -u root
mysql -h localhost -u root $2 < $1

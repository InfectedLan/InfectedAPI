if [ $# -eq 0 ]
	then
    	echo "Usage: installDb.sh [file] [database] <password>(optional)"
  	else
	  	if [ -z "$3" ]
			then
				echo "No password provided, travis mode it is"
				echo "create database $2" | mysql -h localhost -u root
				mysql -h localhost -u root $2 < $1
			else
				echo "Assuming home pc, dropping and re-creating"
				echo "drop database $2" | mysql -h localhost -u root -p$3
				echo "create database $2" | mysql -h localhost -u root -p$3
				mysql -h localhost -u root -p$3 $2 < $1
		fi
fi

echo "Patching configs"
sed -i "s/\/home\/' . self::domain . '\/public_html\/api\//\/home\/travis\/build\/InfectedLan\/InfectedAPI\//g" settings.php

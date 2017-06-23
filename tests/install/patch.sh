echo "Patching configs"
sed -i "s/\/home\/' . self::domain . '\/public_html\/api\//~\//g" settings.php

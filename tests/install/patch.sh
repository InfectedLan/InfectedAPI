echo "Patching configs"
sed -i "s/const api_path = '/home/' . self::domain . '/public_html/api/';/const api_path = '~/';/g" settings.php

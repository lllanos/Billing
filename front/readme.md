Pre-requisitos:

	Laravel 5.4
    PHP >= 7.1.3
	  Node

	Extensiones:
    BCMath PHP Extension
    Ctype PHP Extension
    JSON PHP Extension
    Mbstring PHP Extension
    OpenSSL PHP Extension
    PDO PHP Extension
    Tokenizer PHP Extension
    XML PHP Extension

	Apache debe tener activado: mod_rewrite:
		http://www.inprose.com/en/articles/7-how-to-enable-modrewrite-in-apache-on-fedora.html

	OpenSSL debe estar activo en php y apache

------------------------------------------------

------------------------------------------------
	1) Agregar SSH Key
		Agregar la key al sistema para poder actualizar el código del sistema compartido (concursar-package)
			eval $(ssh-agent -s)
			ssh-add ssh_keys/id_rsa
			cp ssh_keys/* ~/.ssh

------------------------------------------------
	2) composer install

		En server linux:
			php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
			php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
			php composer-setup.php
			php -r "unlink('composer-setup.php');"

------------------------------------------------
	3) Configuracion del ambiente:

		a) Crear una copia de .env.example (llamarlo .env)
			sudo cp .env.example .env

		b) Generar Application key
			php artisan key:generate

  	c) Dar accesos a storage y bootstrap/cache:
  		chmod -R 0777 storage bootstrap/cache


------------------------------------------------
	4) Node

		a) Instalar node o actualizarlo a la última versión.


------------------------------------------------
	5) Crear base de datos:

		a) Crear base de datos y configurar la conexión en .env

		b) Correr migrations y seeds:
			php artisan migrate --seed

------------------------------------------------
	6) Configurar link de descarga:
		php artisan storage:link

---------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------
	Instalación en servidor:
		a) Dejar corriendo jobs
			 nohup php artisan queue:work --tries=1  --queue=default --daemon &

		b) Agregar Schedule Tasks
			 crontab -e

			* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1

<VirtualHost *:80>
	DocumentRoot #phing:paths.protected#
	ServerName adm.#phing:hosts.root#
	ErrorLog logs/adm.#phing:hosts.root#-error_log
	CustomLog logs/adm.#phing:hosts.root#-access_log common

	<Directory "#phing:paths.protected#">
		DirectoryIndex index.php index.html
		AllowOverride FileInfo AuthConfig Limit Indexes Limit
		Options FollowSymLinks Indexes
		Options +Includes
	</Directory>
</VirtualHost>
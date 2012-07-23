<VirtualHost *:80>
	DocumentRoot #phing:paths.protected#
	ServerName #phing:hosts.protected#
	ErrorLog logs/#phing:hosts.protected#-error_log
	CustomLog logs/#phing:hosts.protected#-access_log common

	<Directory "#phing:paths.protected#">
		DirectoryIndex index.php index.html
		AllowOverride FileInfo AuthConfig Limit Indexes Limit
		Options FollowSymLinks Indexes
		Options +Includes
	</Directory>
</VirtualHost>
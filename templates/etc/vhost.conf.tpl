<VirtualHost *:80>
	DocumentRoot #phing:paths.public#
	ServerName #phing:hosts.root#
	ErrorLog logs/#phing:hosts.root#-error_log
	CustomLog logs/#phing:hosts.root#-access_log common

	<Directory "#phing:paths.public#">
		DirectoryIndex index.php index.html
		AllowOverride FileInfo AuthConfig Limit Indexes Limit
		Options FollowSymLinks Indexes
		Options +Includes
		AddType application/x-httpd-php .php
	</Directory>
</VirtualHost>
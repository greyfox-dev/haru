Haru
====

Born in the spring


# PhingX


Дополнительные tasks для обеспечения работы со свойствами porperties проекта, автосборки проектов miao.

### XmlMergeTask

Позволяет "склеить" несколько xml файлов в один. Используется для формирование свойств 
в зависимости от типа платформы (dev, test, prod) и дополнительных пользовательских настроек.

Пример типичный пример файлов свойств:

* common.xml - содержит все свойства.
* extends/develop.xml - содержит переопредленный свойства, присущие *dev* платформе.
* extends/users/user.xml - содержит пользовательские настройки

Используя данный таск мы можем получать разные конфигурационные файлы свойств проекта.


    //file: common.xml
    <config>
      <display_errors>0</display_errors>
	    <email>prod@project.com</email>
    </config>

    //file: extends/develop.xml
    <config>
        <display_errors>1</display_errors>	
    </config>

    //file: build.xml
    ...
    <xmlmerge srcFileList="common.xml,extends/develop.xml" dstFile="result.xml" />
    ...
    
    //file: result.xml
    <config>
	    <display_errors>1</display_errors>
	    <email>prod@project.com</email>
    </config>
    
### XmlPropertyResolveTask

Предназначен для генерации файла свойств d форматах ini, php, xml из исходного файла свойств с ссылками.

Пример
	//file: build/property.xml
	<config>
		<path>
			<root>/www/project</root>
			<tmp>${config.path.root}/tmp</tmp
		</path>
	<config>
	
	...
	<xmlmerge srcFileList="build/property.xml" dstFile="config/property.xml" type="xml" />
	...
	
	//file: config/property.xml
	<config>
		<path>
			<root>/www/project</root>
			<tmp>/www/project/tmp</tmp
		</path>
	<config>
	
Формат *xml* универсален, пригодится для perl, python и др. языков, 
*php* - для того что сократить время для разбора xml в php-скриптах.
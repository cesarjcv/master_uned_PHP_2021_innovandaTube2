# InnovandaTube2 #

## Instalación ##

### Máquinas virtuales Docker ###
Ejecutar lo siguiente desde la consola en la carpeta raíz del proyecto
~~~
docker-compose up -d --build sistema
~~~

### Descarga de bibliotecas y ajuste de código ###
Ejecutar dentro de la carpeta __src__
~~~
- docker-compose run --rm composer install 
- docker-compose run --rm npm install 
~~~
__NOTA:__ en sistemas hay UNIX hay que tener permisos de administrador.  
Modificar la línea 114 en el archivo _src\vendor\jotaelesalinas\laravel-adminless-ldap-auth\src\LdapHelper.php_  
cambiar `$userdn = sprintf($this->user_full_dn_fmt, $user_ldap_attribs[$this->bind_field]);`  
por `$userdn = sprintf($this->user_full_dn_fmt, $identifier);`
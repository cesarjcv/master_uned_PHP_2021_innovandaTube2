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
docker-compose run --rm composer install 
docker-compose run --rm npm install 
~~~
__NOTA:__ en sistemas hay UNIX hay que tener permisos de administrador.  
  
Modificar la línea 114 en el archivo _src\vendor\jotaelesalinas\laravel-adminless-ldap-auth\src\LdapHelper.php_  
cambiar `$userdn = sprintf($this->user_full_dn_fmt, $user_ldap_attribs[$this->bind_field]);`  
por `$userdn = sprintf($this->user_full_dn_fmt, $identifier);`
  
Creación de tablas en base datos:
~~~
docker-compose run --rm artisan migrate
~~~
## Acceso ##

### Administración ###
URL: http://localhost/admin  
__Usuario:__ tesla  
__Clave:__ password
#### Canales ####
Para añadir un canal tenemos las siguientes posibilidades de entrada en el formulario:
1. Por código de canal (ChannelId):
    1. Introduciendo el código directamente: `UCAQZ4iOfUvCQmgtd_a69N9w`
    2. Dirección URL con código: `https://www.youtube.com/channel/UCAQZ4iOfUvCQmgtd_a69N9w/videos`
2. Por nombre de usuario (pueden ser varios canales):
    1. Dirección URL: `https://www.youtube.com/user/IslasDeCultura`
3. Por url de canal (/c/):
    1. Dirección URL con carácteres especiales: `https://www.youtube.com/c/GarajeHermético/*`
    2. Dirección URL con carácteres transformados: `https://www.youtube.com/c/GarajeHerm%C3%A9tico/*`
  
Para eliminar un canal pulsar sobre el icono de la papelera en el mismo.

## Tareas en segundo plano ##
Para que se ejecuten las tareas en segundo plano hay que ejecutar los siguientes comandos:
~~~
docker-compose run --rm artisan schedule:worker
docker-compose run --rm artisan queue:worker
~~~
El primero se encarga de planificar las tareas e introducirlas en la cola de trabajo. El segunda ejecuta los comandos de la cola de trabajo.
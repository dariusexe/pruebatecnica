Configuración y uso

Para la configuración y pueta en marcha del software tan solo hay que ejecutar un docker-composer up -d en el directorio del archivo docker-compose.yml

He optado por crear una API CRUD con diferentes endpoint para su uso.

ejecutando el comando php artisan route:list se puede ver todos los endpoint que contien la actividad


Siguiendo las especificaciones de la prueba tenemos endpoint que se encarga de devolver los datos pedidos

 
Añadir actividad a un proyecto.
POST            api/projects/{project}/activities ..... activities.store › ActivityController@store

Añadir incidencia a una actividad.
POST            api/projects/{project}/activities/{activity}/incidents ..... incidents.store › IncidentController@store

Asignar usuarios a proyectos, actividades e incidencias.

Projectos
GET|HEAD        api/projects/{project}/managers ..... ProjectController@getManagers
POST            api/projects/{project}/users ....ProjectController@addParticipant
GET|HEAD        api/projects/{project}/users ..... ProjectController@getParticipants
DELETE          api/projects/{project}/users/{user} ..... ProjectController@removeParticipant

Actividades
POST            api/projects/{project}/activities/{activity}/users ..... ActivityController@addParticipant
GET|HEAD        api/projects/{project}/activities/{activity}/users .....ActivityController@getParticipants
DELETE          api/projects/{project}/activities/{activity}/users/{user} ...... ActivityController@removeParticipant
POST            api/projects/{project}/activities/{activity}/users/{user}/role ..... ActivityController@changeParticipantRole

Incidencias
POST            api/projects/{project}/activities/{activity}/incidents/{incident}/users ...... IncidentController@addParticipant
GET|HEAD        api/projects/{project}/activities/{activity}/incidents/{incident}/users ...... IncidentController@getParticipants
DELETE          api/projects/{project}/activities/{activity}/incidents/{incident}/users/{user} ..... IncidentController@removeParticipant
Listar actividades en las que participa un usuario.
GET|HEAD        api/users/{user}/activities ..... UserController@showActivities

Listar incidencias a las que un usuario tiene acceso.
GET|HEAD        api/users/{user}/incidents ..... UserController@showIncidents

Listar participantes de un proyecto.
GET|HEAD        api/users/{user}/projects ..... UserController@showProjects

 

Tambien tiene una batería de pruebas que se pueden lanzar con el comando php artisan test.





Comentarios

La experiencia que tengo con Laravel se remonta a la versión 5, pero leyendo las nuevas versiones he podido utilizar nuevas herramientas que han hecho que puedo relizar tareas mas fácilmente.

Para la autenticación he utilizado el macanismo de seguridad JWT ya que me siento cómodo utilizandolo.

Siguiendo la lógica, los creadores de una actividad o proyecto son por defecto administradores del recurso que crean.

Dentro de las policies hay mucho codigo repetido, con vista a futuro si queremos cambiar una politica sobre un recurso es mas rápido cambiar la funcion asociada.


Datos de Contacto

Darío Vallejo Carrasco
email:darioinfor@gmail.com
tlf: 684163502





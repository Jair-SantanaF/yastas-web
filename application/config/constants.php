<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 23 mar 2018
 *	Nota: Constante para configurar el API de firebase
 ***********************************************************************/
 //api para yastas
  //define('API_ACCESS_KEY','AAAAZ0Rz0cQ:APA91bFNjBmVZNR2gWokDvnDh_9LbFACmVIjcAaDxleEYQSWjIhgbAGtT02-uPLppzQbx51nsiDwdmPEOY9HSzbeQ7TYF0moceTwr2NQXfE8HLMlqvJ5qKiKSprGhED7634cJoWhGgHV');
 //esta es el api que se necesita para nuup
    // define('API_ACCESS_KEY','AAAA24Xaxxw:APA91bHiMWTLJJnrkGlmyO1-6KCqcC4mOnjU8-pJwf2zKp431cwhtR7jXGXmg7jkVqaqQ46VDW_Lz-TkqzaZrt7CC4Y9j4cf6oKGAx-ZUZVt0cBtm_60rHENqE5d3vQMDXfAR5PUBU3S');
 define("API_ACCESS_KEY",'ASDF');
//estas no se usan o no funcionan
//key que estaba en el proyecto (AL PARECER ES SOLO PARA NUUP)
//define('API_ACCESS_KEY', 'AAAAivNJ3m8:APA91bFdtf3M1V0KrNdCHvfRnwQkxAf6D138TszbXkJ7FIwnheQxDI3T8ll6Etk5geTniqFPHCPnUUsMyVFETTD2ampgHO6iS8ejLovKiZApNEoW0RDKaC-GM5eGMyciakmRFdYQFPXR');
//esta es la key que esta primero para nuup
//define('API_ACCESS_KEY','AAAAivNJ3m8:APA91bFXDu_y08RjGASREAYN8WdH222DQwyz3mjRjqW7i_9rEe4CXqy6yPzLf-ma_Zqt2oxPL8IwG9T3zmy6MSUWf5nN04zufnnfVWIzh6U3GEURor64qmsukSvAZ9A-13dQWYm05hFv');
//key que mando rodo
// define('API_ACCESS_KEY', 'eHNOBQ0a6UJSsTyr2Kl84S:APA91bERr2DJeAJJFGHhpD3_4rz_han4AkZZeennjA9chFegnVX5ihpEnKdS7I4TMn9LNN2JCWXTChRVox6p3ejP6eQc0kjbUxeX9gucitEAcAmdM970cqBDj77uH7PnxhtwQeYUwPjB');
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 05 abr 2018
 *	Nota: Constantes para el envio de correo por phpmailer
 ***********************************************************************/
define('EMAIL', 'prueba@kreativeco.com');
define('PASSWORD_EMAIL', '123456');
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 06 abr 2018
 *	Nota: Constante para obtener el key para desencriptar password
 ***********************************************************************/
define('KEY_AES','kreativecomexico');

define('ENVIRONMENT_CUSTOM', 'development');
define('PHOTO_URL_NUPI', (ENVIRONMENT_CUSTOM == 'development') ? '/qa-nuup/uploads/' : '');
define('PHOTO_URL_BIMBO', (ENVIRONMENT_CUSTOM == 'development') ? '/qa-bimbo-nuup/uploads/' : '');

define('BASE_URL_NUPI', (ENVIRONMENT_CUSTOM == 'development') ? '/qa-nuup'.'/' : '');
define('BASE_URL_BIMBO', (ENVIRONMENT_CUSTOM == 'development') ? '/qa-bimbo-nuup'.'/' : '');

define('BASE_URL',  (ENVIRONMENT == "development")? 'http://localhost/nuup_respaldo/' :    'https://kreativeco.com/qa-nuup/');
define('BASE_PATH',(ENVIRONMENT == 'development') ? "c:/xampp/htdocs/nuup_respaldo/" : '/home/kreati22/public_html/qa-nuup/');

// define('BASE_PATH', '/home/kreati22/public_html/qa-nuup/');

// >>>>>>> 033e550c564992ee8adfadcd37d932429811be7b
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 10/07/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Constantes para los roles actuales(Roles para permisos en
 *          admin).
 ***********************************************************************/
define('ROL_ADMINISTRADOR_PRINCIPAL', 1);
define('ROL_ADMINISTRADOR_EMPRESA', 2);
define('ROL_INTEGRANTE', 3);
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/08/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: COnstantes para tipo de usuarios de registro
 ***********************************************************************/
define('USER_INTERNAL', 1);
define('USER_EXTERNAL', 2);
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Constante para tipos de respuestas de preguntas
 ***********************************************************************/
define('TIPO_PREGUNTA_DIBUJO',10);
define('TIPO_PREGUNTA_SUBIR_IMAGEN',11);
define('TIPO_PREGUNTA_MULTIPLE_IMAGEN',3);
define('TIPO_PREGUNTA_SEMAFORO',4);
define('TIPO_PREGUNTA_UNICA_IMAGEN',5);
define('TIPO_PREGUNTA_CAJON',6);
define('TIPO_PREGUNTA_LIKE_NUMEROS',14);
define('TIPO_PREGUNTA_LIKE_CARAS',8);
define('TIPO_PREGUNTA_TACHE_PALOMA',7);
define('TIPO_PREGUNTA_PROPORCIONES',9);
define('TIPO_PREGUNTA_ABIERTA',13);
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Constantes tipos de paquetes
 ***********************************************************************/
define('PAQUETE_SIN_RESTICCIONES',5);
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Contantes para los tipos de categorias de un quiz
 ***********************************************************************/
define('QUIZ_CATEGORY_GENERAL',1);
define('QUIZ_CATEGORY_LIBRARY',2);
define('QUIZ_CATEGORY_ELEARNING',3);
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 23/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Constantes para segmentacion de usuarios por empresa
 ***********************************************************************/
define('EMPRESA_INTERNOS',500);
define('EMPRESA_EXTERNOS',501);
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Constantes de los ids de los servicios
 ***********************************************************************/
define('SERVICE_WALL',3);
define('SERVICE_LIBRARY',4);
define('SERVICE_GAMES',5);
define('SERVICE_FEEDBACK',8);
define('SERVICE_RANKING',9);
define('SERVICE_EVENTS',10);
define('SERVICE_QUESTIONS',11);
define('SERVICE_ELEARNING',12);
define('SERVICE_CHAT',23);
define('SERVICE_COMUNIDAD',14);
define('SERVICE_RULETA',1002);
define('SERVICE_SERPIENTES',1004);
define('SERVICE_RUN_PANCHO',1005);
define('SERVICE_RETOS',1006);
define('SERVICE_AHORCADO',1007);
define('SERVICE_GAME_SNAKE',1008);
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/17/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Constante para default tipografia en nuup en cuestionarios
 ***********************************************************************/
define('DEFAULT_STYLE_QUESTIONS','font-family:  Helvetica, Sans-Serif; color:white!important; font-size:9pt; text-align: center;');
define('DEFAULT_STYLE_QUESTIONS_ANSWERS','font-family:  Helvetica, Sans-Serif; color:white!important; text-align: center;');

define('URL_FAQS', 'http://kreativeco.com/qa-nuup/uploads/');
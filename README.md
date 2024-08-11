<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Post instalación

1. Ejecutar `php artisan init`

## Instalación

1. Verificar la rama en la que estas actualmente `git branch`
2. Cambiarte de rama en la que vas a trabajar `git checkout {nombre de la rama}`
3. Duplicar `.env.example` y cambiar el nombre a `.env`
4. Ejecutar `php artisan key:generate` para generar la llave
5. Despues instalar las librerias `composer install`
6. Realizar las migraciones `php artisan migrate`
7. Eliminar y crear la base de datos con los factories y seeders `php artisan migrate:fresh --seed`
8. Levantar el servidor enbebido `php artisan serve`

## Crear el crud de ordersproducts

1. Primero se crea la migracion `php artisan make:migration create_OrdersProducts_table`
2. Se modifica segun el modelo
3. Se crea el modelo
4. Se crea el controller
5. Se crea los request a utilizar
6. Se hace uso de la clase `ApiResponseHelper`
7. Se configura los factories
8. Se configura los seeders
9. Se agrega la ruta

## Instalación de Swagger

1. Añadir el paquete de swagger `composer require darkaonline/l5-swagger`
2. ir a la carpeta bootstrap/providers.php y copiar `L5Swagger\L5SwaggerServiceProvider::class,`
3. Publicar los archivos de configuracion `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`
4. se puede ver la ruta api/documentation en `php artisan r:l`
5. Agregar todas las configuraciones en el controller, se encuentra en `configuracion.txt`
6. guardar y  generar la documentacion `php artisan l5-swagger:generate`
7. ejecutar el servidor `php artisan serve`
8. para que se guarde los cambios automaticamente, agregar en el archivo env `L5_SWAGGER_GENERATE_ALWAYS=true`



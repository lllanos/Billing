# **Implementación Sistema Billing**

### Sobre el contenido

En la carpeta del entregable se encuentran 3 carpetas

- Archivo YML de despliegue para Docker compose
- Archivo .env
- Carpeta Db
- Carpeta Back conteniendo el proyecto Laravel correspondiente al administrador
- Carpeta Front conteniendo el proyecto Laravel correspondiente a la aplicación del contratista

Importante

Este instructivo es para uso exclusivo de la implementación y solo contempla la construcción de los contenedores correspondientes al backend y frontend y no una implementación completa con base de datos

En la base de datos no se realizó ninguna modificación por lo que en este instructivo no vamos a realizar la construcción del contenedor de la base de datos

En caso de que se quiera realizar una implementación completa remitirse al instructivo de la entrega anterior

Por otro lado, se informa que los contenedores están configurados para exponer las aplicaciones de backend y frontent en los puertos 8011 y 9011 respectivamente

En caso de que se quiera modificar los puertos deben modificarse desde el archivo .yml del Docker compose

***Paso 1***

Descomprimir el entregable en una carpeta de trabajo temporal

***Paso 2***

Copiar los siguientes archivos y carpetas a la carpeta de implementación utilizada en la implementación previa

Solo vamos a reemplazar:

·    Carpeta Back

·    Carpeta Front

·    Archivo YML

·    Archivo .env

Estas carpetas y archivos son los que vamos a reemplazar en la carpeta correspondiente a la utilizada en la implementación anterior

***Paso 3***

Modificar Rutas de aplicación en el archivo .env suministrado en la carpeta raíz de la entrega

Las rutas configuradas actualmente corresponden a la que publica el docker

```
**APP_URL=http://localhost:9011**

**URL_BACK=http://localhost:8011**
```

Adecuar estas url a las utilizadas para acceder a la aplicación por parte de los usuarios finales

***Paso 4***

Una vez reemplazados los archivos y carpetas indicados en el paso anterior abrimos una consola del sistema operativo y nos posicionamos en la ubicación del archivo .yml del Docker compose

***Paso 5***

Ejecutamos la siguiente línea de comando:

```
docker compose build –no-cache backend
```

presionamos enter y esperamos que termine

***Paso 6***

Una vez finalizada la instrucción anterior ejecutamos el siguiente comando

```
docker compose build –no-cache frontend
```

Presionamos enter y esperamos que el comando finalice

***Paso 7***

Una vez finalizada la ejecución del comando ejecutamos el siguiente comando

```
docker compose up -d
```

***Paso 8***

Cerramos la consola 

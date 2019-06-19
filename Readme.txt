composer create-project --prefer-dist laravel/laravel PHPAPI_JWT

composer require tymon/jwt-auth:dev-develop --prefer-source

php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

php artisan jwt:secret

Schema::create('products', function (Blueprint $table) {
     $table->bigIncrements('id');
     $table->bigInteger('user_id')->unsigned();;
     $table->string('name')->unique();
     $table->text('description');
     $table->decimal('price',10,2);
     $table->integer('quantity');
     $table->timestamps();
          
     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

});

php artisan migrate

php artisan serve
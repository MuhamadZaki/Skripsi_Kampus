Aplikasi Surat

Installing
----------
1. Extract file laravel-mailing-app-zaki-main.zip
2. Copy direktori hasil extract ke htdocs (XAMPP) atau Lainnya
3. Ubah nama file .env.example menjadi .env
4. Apabila belum menginstall composer, maka download disini -> https://getcomposer.org/download/
5. Jalankan perintah pada terminal/cmd -> composer install
6. Jalankan perintah pada terminal/cmd -> php artisan key:generate
7. Atur variable (DB_DATABASE=namadb, DB_USERNAME=username, DB_PASSWORD=password) pada file .env, sesuaikan dengan pengaturan database yang ada
8. Jalankan perintah pada terminal/cmd -> php artisan migrate
9. Jalankan perintah pada terminal/cmd -> php artisan storage:link
10. Jalankan perintah pada terminal/cmd -> php artisan db:seed (Opsional)
11. Jalankan perintah pada terminal/cmd -> php artisan serve dan php artisan queue:work

#Skripsi Kampus
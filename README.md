Εγκατάσταση και ρύθμιση XAMPP: 
Η έκδοση του προγράμματος πρέπει να είναι συμβατή με αυτή του λειτουργικού 
συστήματος του χρήστη. Η εγκατάσταση του λογισμικού πρέπει να γίνει από τον παρακάτω 
σύνδεσμο και με τις προεπιλεγμένες επιλογές ή τουλάχιστον τις εξης: 
Apache, php, MySQL και  phpMyAdmin. 
https://www.apachefriends.org/index.html 
Τώρα, στον φάκελο phpMyAdmin, στο directory εγκατάστασης του xampp (default: 
C:\xampp), στο αρχείο config.inc.php ο χρήστης πρέπει να ρυθμίσει τον κωδικό 
για την σύνδεση στον MySQL server σε “admin123” στην γραμμή 21. 
Αρχικό: 
$cfg['Servers'][$i]['password'] = ''; 
Τελικό: 
$cfg['Servers'][$i]['password'] = 'admin123'; 
Έναρξη διακοσμητών: 
Από το περιβάλλον διεπαφής, της εφαρμογής XAMPP ο χρήστης πρέπει να 
εκκινήσει τους server apache και MySQL. Έπειτα να αλλάξει τον κωδικό στον 
MySQL server με τις ακόλουθες εντολές στην γραμμή εντολών. 
mysql –u root 
ALTER USER 'root'@'localhost' IDENTIFIED BY 'admin123'; 
FLUSH PRIVILEGES; 
Εγκατάσταση σελίδας: 
Τέλος, στον φάκελο (directory) που επέλεξε ο χρήστης να εγκαταστήσει το 
λογισμικό πρέπει να βρει τον φάκελο “htdocs” και να επικολλήσει τον φάκελο 
από το παραδοτέο (erasmus portal). 
Η ιστοσελίδα πρέπει τώρα να είναι διαθέσιμη στο: 
http://localhost/erasmus portal/index.php

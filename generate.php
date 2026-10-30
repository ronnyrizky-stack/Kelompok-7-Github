
<?php
// Jalankan satu kali saat setup.
// PERHATIAN: tabel student/admin dihapus lalu dibuat ulang.

$c = mysqli_connect("localhost", "root", "");

if (!$c) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_query($c, "CREATE DATABASE IF NOT EXISTS my_campus")
    or die(mysqli_error($c));

mysqli_select_db($c, "my_campus");

// Menghapus tabel lama
mysqli_query($c, "DROP TABLE IF EXISTS student");
mysqli_query($c, "DROP TABLE IF EXISTS admin");

// Membuat tabel student
$sql = "CREATE TABLE student (
    nim CHAR(8) PRIMARY KEY,
    name VARCHAR(100),
    birth_city VARCHAR(50),
    birth_date DATE,
    faculty VARCHAR(50),
    department VARCHAR(50),
    gpa DECIMAL(3,2)
)";

mysqli_query($c, $sql) or die(mysqli_error($c));

// Membuat tabel admin
mysqli_query(
    $c,
    "CREATE TABLE admin (
        username VARCHAR(50) PRIMARY KEY,
        password CHAR(40)
    )"
) or die(mysqli_error($c));

// Menambahkan akun admin
$u = "admin";
$p = sha1("adminmahasiswa");

$st = mysqli_prepare($c, "INSERT INTO admin VALUES (?, ?)");

mysqli_stmt_bind_param($st, "ss", $u, $p);
mysqli_stmt_execute($st);
mysqli_stmt_close($st);

// Data mahasiswa
$data = [
    [
        "10101001",
        "Andi Saputra",
        "Surabaya",
        "2004-01-12",
        "FTIB",
        "Sistem Informasi",
        3.50
    ],
    [
        "10101002",
        "Budi Santoso",
        "Sidoarjo",
        "2004-03-22",
        "FTIB",
        "Informatika",
        3.10
    ],
    [
        "10101003",
        "Citra Lestari",
        "Malang",
        "2003-07-09",
        "FTEIC",
        "Teknik Elektro",
        3.40
    ],
    [
        "10101004",
        "Dewi Anggraini",
        "Gresik",
        "2004-11-18",
        "FTIB",
        "Sistem Informasi",
        3.75
    ],
    [
        "10101005",
        "Eko Prasetyo",
        "Surabaya",
        "2003-05-30",
        "FTEIC",
        "Informatika",
        3.60
    ]
];

// Memasukkan data mahasiswa ke tabel student
$st = mysqli_prepare(
    $c,
    "INSERT INTO student VALUES (?, ?, ?, ?, ?, ?, ?)"
);

foreach ($data as $d) {
    mysqli_stmt_bind_param(
        $st,
        "ssssssd",
        $d[0],
        $d[1],
        $d[2],
        $d[3],
        $d[4],
        $d[5],
        $d[6]
    );

    mysqli_stmt_execute($st);
}

mysqli_stmt_close($st);

// Pesan berhasil
echo "Database my_campus dan tabel berhasil dibuat. ";
echo "Login: admin / adminmahasiswa";

mysqli_close($c);
?>

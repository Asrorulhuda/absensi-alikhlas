/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 10.4.25-MariaDB : Database - sp_siswa
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `data_absen` */

DROP TABLE IF EXISTS `data_absen`;

CREATE TABLE `data_absen` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL DEFAULT current_timestamp(),
  `jam_masuk` time NOT NULL DEFAULT current_timestamp(),
  `ket_masuk` varchar(100) DEFAULT NULL,
  `jam_keluar` time DEFAULT current_timestamp(),
  `ket_keluar` varchar(100) DEFAULT NULL,
  `uid` varchar(40) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `keterangan` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `data_absen` */

insert  into `data_absen`(`id`,`tanggal`,`jam_masuk`,`ket_masuk`,`jam_keluar`,`ket_keluar`,`uid`,`status`,`keterangan`) values 
(1,'2024-07-15','00:14:36','','00:00:00','','AFFE2B8C','IN','HADIR'),
(2,'2024-07-15','00:14:56','','00:00:00','','83E5996AB','IN','HADIR'),
(3,'2024-07-15','00:15:28','Terlambat','00:00:00','','CA14526672','IN','HADIR'),
(4,'2024-07-15','00:20:43','Sangat Terlambat','00:00:00','','6544321112','IN','ABSEN');

/*Table structure for table `data_guru` */

DROP TABLE IF EXISTS `data_guru`;

CREATE TABLE `data_guru` (
  `g_id` int(20) NOT NULL AUTO_INCREMENT,
  `g_nip` varchar(20) NOT NULL,
  `g_nama` varchar(100) NOT NULL,
  `g_tgl_lahir` date NOT NULL,
  `g_kelamin` varchar(50) NOT NULL,
  `g_jabatan` varchar(50) NOT NULL,
  `g_status` varchar(50) NOT NULL,
  `g_mail` varchar(100) NOT NULL,
  `g_contact` varchar(20) NOT NULL,
  `g_phone` varchar(20) NOT NULL,
  `g_kompetensi` varchar(100) NOT NULL,
  `g_picture` text NOT NULL,
  `g_tgs_tambahan` varchar(30) NOT NULL,
  `g_alamat` text NOT NULL,
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

/*Data for the table `data_guru` */

insert  into `data_guru`(`g_id`,`g_nip`,`g_nama`,`g_tgl_lahir`,`g_kelamin`,`g_jabatan`,`g_mail`,`g_contact`,`g_kompetensi`,`g_picture`,`g_tgs_tambahan`,`g_alamat`) values 
(1,'1993022620200510004','Asiyah, S.Pd.','1993-02-26','Perempuan','Guru','aisyah123@gmail.com','087654322111','Bahasa Inggris','../../assets/img/guru_pict/1993022620200510004_1721116301.png','Urs. Sarpras','Jl. Taman Kebon Sirih 1 No.3 4, RT.10/RW.10, Kampung Bali, Tanah Abang, Central Jakarta City, Jakarta 10250'),
(2,'1994012020200510002','Siti Istiqomah, S.Pd','1994-01-20','Perempuan','Guru','istiqomah_1321@gmail.com','087654322111','Bahasa Indonesia','../../assets/img/guru_pict/1994012020200510002_1721113680.png','Urs. Kesiswaan.','Jalan Hayam Wuruk No.126 Glodok, RT.2/RW.6, Mangga Besar, Kec. Taman Sari, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta');

/*Table structure for table `data_holiday` */

DROP TABLE IF EXISTS `data_holiday`;

CREATE TABLE `data_holiday` (
  `h_id` int(40) NOT NULL AUTO_INCREMENT,
  `h_date` date NOT NULL,
  `h_name` text DEFAULT NULL,
  PRIMARY KEY (`h_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `data_holiday` */

/*Table structure for table `data_invalid` */

DROP TABLE IF EXISTS `data_invalid`;

CREATE TABLE `data_invalid` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL DEFAULT current_timestamp(),
  `waktu` time NOT NULL DEFAULT current_timestamp(),
  `uid` varchar(40) NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `data_invalid` */

/*Table structure for table `data_siswa` */

DROP TABLE IF EXISTS `data_siswa`;

CREATE TABLE `data_siswa` (
  `s_id` int(50) NOT NULL AUTO_INCREMENT,
  `s_uid` varchar(40) DEFAULT NULL,
  `s_nama` varchar(100) DEFAULT NULL,
  `s_nis` varchar(20) DEFAULT NULL,
  `s_kelamin` varchar(50) DEFAULT NULL,
  `s_tgl_lahir` date DEFAULT NULL,
  `s_phone` varchar(15) DEFAULT NULL,
  `s_alamat` text DEFAULT NULL,
  `s_kontak_wali` varchar(20) DEFAULT NULL,
  `s_nama_wali` varchar(100) DEFAULT NULL,
  `s_picture` text DEFAULT NULL,
  `s_jurusan` varchar(20) DEFAULT NULL,
  `s_kelas` varchar(10) DEFAULT NULL,
  `s_status` varchar(20) DEFAULT NULL,
  `s_created` date NOT NULL DEFAULT current_timestamp(),
  `user_stat` int(10) DEFAULT NULL,
  PRIMARY KEY (`s_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

/*Data for the table `data_siswa` */

insert  into `data_siswa`(`s_id`,`s_uid`,`s_nama`,`s_nis`,`s_kelamin`,`s_tgl_lahir`,`s_phone`,`s_alamat`,`s_kontak_wali`,`s_nama_wali`,`s_picture`,`s_jurusan`,`s_kelas`,`s_status`,`s_created`,`user_stat`) values 
(1,'C199865433','Nami Kania','1356678991','Perempuan','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','https://i.pinimg.com/736x/5c/61/e3/5c61e3e497d152534dc6cf0f1aa7ab3a.jpg','2','IX','Aktif','2024-07-15',0),
(2,'D1345562','Veronika Zucha','1356678988','Perempuan','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','https://kacamataminusjogja.com/wp-content/uploads/2017/10/kacamata-wanita-jogja.jpg','1','IX','Aktif','2024-07-15',0),
(3,'C4522345','Dania Ratnawati','1356678988','Perempuan','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','https://pijatpanggilancitra.com/wp-content/uploads/2019/12/Pijat-Panggilan-Jogja-082135846699-1-2-706x675.jpg','2','IX','Aktif','2024-07-15',0),
(4,'DC1577812121','Johan Manami','1356678987','Laki-laki','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','../../assets/img/user_pict/DC1577812121_1720982018.png','1','VII','Aktif','2024-07-15',0),
(5,'AAC1345562','Niken Ayu Pertiwi','1356678766','Perempuan','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','085743614587','Orang Tua','https://situmassage.com/wp-content/uploads/2023/06/pijat-panggilan-jogja-24-jam-terapis-jogjakarta-istimewa.jpg','1','VII','Aktif','2024-07-14',0),
(6,'567C312333','Wida Kurniati','1356645662','Perempuan','2006-08-01','085743614587','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','08122345555','Orang Tua','https://media.monolithicpower.com/wysiwyg/employee-img2.png','1','VIII','Aktif','2024-07-14',0),
(7,'6544321112','Kartika Dyah Kumala','1356678988','Perempuan','2006-08-06','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','https://i.pinimg.com/736x/b8/80/c7/b880c751c5107d04dc112e909f97d39e.jpg','1','VIII','Aktif','2024-07-15',0),
(8,'E1F2456788','Donny Haryono','1356678987','Laki-laki','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','08122345555','Orang Tua','https://media.monolithicpower.com/wysiwyg/employee-img3.png','1','VII','Aktif','2024-07-14',0),
(13,'1345627711','Nami Amelia','1356678991','Perempuan','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','https://thangjamagro.com/wp-content/uploads/2021/03/lois.jpg','1','VII','Aktif','2024-07-15',0),
(14,'CA14526672','Risa Septiana','1356678988','Perempuan','2006-08-01','081245622431','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','https://img-9gag-fun.9cache.com/photo/abVervr_460s.jpg','1','IX','Aktif','2024-07-15',0),
(15,'83E5996AB','Karmila Fatmawati','1356678988','Perempuan','2006-08-01','08223100876','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Siti Nurfadillah','https://thangjamagro.com/wp-content/uploads/2021/03/lois.jpg','1','IX','Aktif','2024-07-15',0),
(16,'CB14526722','Dika Yasna Putra','1356678987','Laki-laki','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Winarto Sudarman','https://media.monolithicpower.com/wysiwyg/employee-img5.png','1','IX','Aktif','2024-07-15',0),
(17,'AFFE2B8C','Dania Rahmawati','1356678766','Perempuan','2006-08-01','081223456677','Jl. Pahlawan No.09, Pagerwojo, Sukomulyo, Kec. Lamongan, Kabupaten Lamongan, Jawa Timur 62216','081234567890','Orang Tua','https://img-9gag-fun.9cache.com/photo/a7E8wqr_460s.jpg','2','IX','Aktif','2024-07-15',0);

/*Table structure for table `opsi_jurusan` */

DROP TABLE IF EXISTS `opsi_jurusan`;

CREATE TABLE `opsi_jurusan` (
  `j_id` int(20) NOT NULL AUTO_INCREMENT,
  `j_short` varchar(10) NOT NULL,
  `j_name` varchar(50) DEFAULT NULL,
  `j_info` text DEFAULT NULL,
  PRIMARY KEY (`j_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Data for the table `opsi_jurusan` */

insert  into `opsi_jurusan`(`j_id`,`j_short`,`j_name`,`j_info`) values 
(1,'A','A','Kelas A'),
(2,'B','B','Kelas B'),
(3,'C','C','Kelas C');

/*Table structure for table `opsi_tk_kelas` */

DROP TABLE IF EXISTS `opsi_tk_kelas`;

CREATE TABLE `opsi_tk_kelas` (
  `tk_id` int(20) NOT NULL AUTO_INCREMENT,
  `tk_name` varchar(50) NOT NULL,
  `tk_ket` varchar(100) NOT NULL,
  PRIMARY KEY (`tk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Data for the table `opsi_tk_kelas` */

insert  into `opsi_tk_kelas`(`tk_id`,`tk_name`,`tk_ket`) values 
(1,'VII','Kelas 7'),
(2,'VIII','Kelas 8'),
(3,'IX','Kelas 9');

/*Table structure for table `system_config` */

DROP TABLE IF EXISTS `system_config`;

CREATE TABLE `system_config` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `company` varchar(100) NOT NULL,
  `sign_in_bg` text NOT NULL,
  `title_bar` varchar(200) NOT NULL,
  `icon_bar` varchar(200) NOT NULL,
  `icon_dashboard` varchar(200) NOT NULL,
  `print_logo` text NOT NULL,
  `footer` varchar(100) NOT NULL,
  `jm_masuk` time NOT NULL,
  `batas_absen_masuk` time NOT NULL,
  `jm_pulang` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `system_config` */

insert  into `system_config`(`id`,`company`,`sign_in_bg`,`title_bar`,`icon_bar`,`icon_dashboard`,`print_logo`,`footer`,`jm_masuk`,`batas_absen_masuk`,`jm_pulang`) values 
(1,'SMP Tunas Unggul','https://thedaarra.com/wp-content/uploads/2021/08/pexels-max-fischer-5212687-1024x683.jpg','Sistem Presensi SMP Tunas Unggul','assets/img/icon_sekolah.ico','assets/img/logo_sekolah.png','assets/img/logo_sekolah.png','SMP Tunas Unggul Jakarta','00:15:00','00:20:00','00:40:00');

/*Table structure for table `tmp_datacard` */

DROP TABLE IF EXISTS `tmp_datacard`;

CREATE TABLE `tmp_datacard` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` varchar(40) NOT NULL,
  `jam` varchar(20) NOT NULL,
  `card_status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `tmp_datacard` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `picture` text DEFAULT NULL,
  `level_akses` varchar(20) NOT NULL,
  `id_siswa` int(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`username`,`password`,`picture`,`level_akses`,`id_siswa`) values 
(1,'Admin Ganteng','john_doe@mail.com','admin','0192023a7bbd73250516f069df18b500','https://pbs.twimg.com/profile_images/917213319882194945/-wEyeLFS.jpg','Admin',0);

/*Table structure for table `wa_notification` */

DROP TABLE IF EXISTS `wa_notification`;

CREATE TABLE `wa_notification` (
  `cnfg_id` int(10) NOT NULL AUTO_INCREMENT,
  `cnfg_token` varchar(50) NOT NULL,
  `cnfg_intro` text NOT NULL,
  `cnfg_status` int(10) NOT NULL,
  PRIMARY KEY (`cnfg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `wa_notification` */

insert  into `wa_notification`(`cnfg_id`,`cnfg_token`,`cnfg_intro`,`cnfg_status`) values 
(1,'Fonte_token','Yth. Wali murid *{nama_siswa}*\r\n\r\nMenginformasikan bahwa {nama_siswa} baru saja melaksanakan *{tipe_presensi}*, tepatnya pukul *{waktu}* dengan status presensi adalah *{status_presensi}*. {ket_tambahan}',0);

/*Table structure for table `v_monthly_report` */

DROP TABLE IF EXISTS `v_monthly_report`;

/*!50001 DROP VIEW IF EXISTS `v_monthly_report` */;
/*!50001 DROP TABLE IF EXISTS `v_monthly_report` */;

/*!50001 CREATE TABLE  `v_monthly_report`(
 `s_uid` varchar(40) ,
 `s_nama` varchar(100) ,
 `tanggal` date ,
 `keterangan` varchar(50) ,
 `1` varchar(1) ,
 `2` varchar(1) ,
 `3` varchar(1) ,
 `4` varchar(1) ,
 `5` varchar(1) ,
 `6` varchar(1) ,
 `7` varchar(1) ,
 `8` varchar(1) ,
 `9` varchar(1) ,
 `10` varchar(1) ,
 `11` varchar(1) ,
 `12` varchar(1) ,
 `13` varchar(1) ,
 `14` varchar(1) ,
 `15` varchar(1) ,
 `16` varchar(1) ,
 `17` varchar(1) ,
 `18` varchar(1) ,
 `19` varchar(1) ,
 `20` varchar(1) ,
 `21` varchar(1) ,
 `22` varchar(1) ,
 `23` varchar(1) ,
 `24` varchar(1) ,
 `25` varchar(1) ,
 `26` varchar(1) ,
 `27` varchar(1) ,
 `28` varchar(1) ,
 `29` varchar(1) ,
 `30` varchar(1) ,
 `31` varchar(1) 
)*/;

/*View structure for view v_monthly_report */

/*!50001 DROP TABLE IF EXISTS `v_monthly_report` */;
/*!50001 DROP VIEW IF EXISTS `v_monthly_report` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_monthly_report` AS select `data_siswa`.`s_uid` AS `s_uid`,`data_siswa`.`s_nama` AS `s_nama`,`data_absen`.`tanggal` AS `tanggal`,`data_absen`.`keterangan` AS `keterangan`,case when dayofmonth(`data_absen`.`tanggal`) = 1 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `1`,case when dayofmonth(`data_absen`.`tanggal`) = 2 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `2`,case when dayofmonth(`data_absen`.`tanggal`) = 3 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `3`,case when dayofmonth(`data_absen`.`tanggal`) = 4 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `4`,case when dayofmonth(`data_absen`.`tanggal`) = 5 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `5`,case when dayofmonth(`data_absen`.`tanggal`) = 6 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `6`,case when dayofmonth(`data_absen`.`tanggal`) = 7 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `7`,case when dayofmonth(`data_absen`.`tanggal`) = 8 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `8`,case when dayofmonth(`data_absen`.`tanggal`) = 9 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `9`,case when dayofmonth(`data_absen`.`tanggal`) = 10 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `10`,case when dayofmonth(`data_absen`.`tanggal`) = 11 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `11`,case when dayofmonth(`data_absen`.`tanggal`) = 12 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `12`,case when dayofmonth(`data_absen`.`tanggal`) = 13 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `13`,case when dayofmonth(`data_absen`.`tanggal`) = 14 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `14`,case when dayofmonth(`data_absen`.`tanggal`) = 15 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `15`,case when dayofmonth(`data_absen`.`tanggal`) = 16 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `16`,case when dayofmonth(`data_absen`.`tanggal`) = 17 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `17`,case when dayofmonth(`data_absen`.`tanggal`) = 18 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `18`,case when dayofmonth(`data_absen`.`tanggal`) = 19 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `19`,case when dayofmonth(`data_absen`.`tanggal`) = 20 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `20`,case when dayofmonth(`data_absen`.`tanggal`) = 21 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `21`,case when dayofmonth(`data_absen`.`tanggal`) = 22 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `22`,case when dayofmonth(`data_absen`.`tanggal`) = 23 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `23`,case when dayofmonth(`data_absen`.`tanggal`) = 24 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `24`,case when dayofmonth(`data_absen`.`tanggal`) = 25 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `25`,case when dayofmonth(`data_absen`.`tanggal`) = 26 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `26`,case when dayofmonth(`data_absen`.`tanggal`) = 27 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `27`,case when dayofmonth(`data_absen`.`tanggal`) = 28 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `28`,case when dayofmonth(`data_absen`.`tanggal`) = 29 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `29`,case when dayofmonth(`data_absen`.`tanggal`) = 30 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `30`,case when dayofmonth(`data_absen`.`tanggal`) = 31 then case when `data_absen`.`keterangan` = 'HADIR' then 'H' when `data_absen`.`keterangan` = 'SAKIT' then 'S' when `data_absen`.`keterangan` = 'CUTI' then 'C' when `data_absen`.`keterangan` = 'IZIN' then 'I' else 'X' end end AS `31` from (`data_siswa` left join `data_absen` on(`data_siswa`.`s_uid` = `data_absen`.`uid`)) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

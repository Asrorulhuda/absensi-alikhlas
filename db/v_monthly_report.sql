CREATE OR REPLACE VIEW v_monthly_report AS
SELECT data_siswa.s_uid,
data_siswa.s_nama,
data_absen.tanggal AS tanggal,data_absen.keterangan,
 CASE WHEN DAY(data_absen.tanggal) = 1 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '1',
  CASE WHEN DAY(data_absen.tanggal) = 2 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '2',
  CASE WHEN DAY(data_absen.tanggal) = 3 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '3',
  CASE WHEN DAY(data_absen.tanggal) = 4 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '4',
  CASE WHEN DAY(data_absen.tanggal) = 5 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '5',
  CASE WHEN DAY(data_absen.tanggal) = 6 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '6',
  CASE WHEN DAY(data_absen.tanggal) = 7 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '7',
  CASE WHEN DAY(data_absen.tanggal) = 8 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '8',
  CASE WHEN DAY(data_absen.tanggal) = 9 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '9',
  CASE WHEN DAY(data_absen.tanggal) = 10 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '10',
  CASE WHEN DAY(data_absen.tanggal) = 11 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '11',
  CASE WHEN DAY(data_absen.tanggal) = 12 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '12',
  CASE WHEN DAY(data_absen.tanggal) = 13 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '13',
  CASE WHEN DAY(data_absen.tanggal) = 14 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '14',
  CASE WHEN DAY(data_absen.tanggal) = 15 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '15',
  CASE WHEN DAY(data_absen.tanggal) = 16 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '16',
  CASE WHEN DAY(data_absen.tanggal) = 17 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '17',
  CASE WHEN DAY(data_absen.tanggal) = 18 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '18',
  CASE WHEN DAY(data_absen.tanggal) = 19 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '19',
  CASE WHEN DAY(data_absen.tanggal) = 20 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '20',
  CASE WHEN DAY(data_absen.tanggal) = 21 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '21',
  CASE WHEN DAY(data_absen.tanggal) = 22 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '22',
  CASE WHEN DAY(data_absen.tanggal) = 23 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '23',
  CASE WHEN DAY(data_absen.tanggal) = 24 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '24',
  CASE WHEN DAY(data_absen.tanggal) = 25 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '25',
  CASE WHEN DAY(data_absen.tanggal) = 26 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '26',
  CASE WHEN DAY(data_absen.tanggal) = 27 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '27',
  CASE WHEN DAY(data_absen.tanggal) = 28 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '28',
  CASE WHEN DAY(data_absen.tanggal) = 29 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '29',
  CASE WHEN DAY(data_absen.tanggal) = 30 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '30',
  CASE WHEN DAY(data_absen.tanggal) = 31 THEN
  CASE
      WHEN data_absen.keterangan = 'HADIR' THEN 'H'
      WHEN data_absen.keterangan = 'SAKIT' THEN 'S'
      WHEN data_absen.keterangan = 'CUTI' THEN 'C'
      WHEN data_absen.keterangan = 'IZIN' THEN 'I'
      ELSE 'X'
  END
  END AS '31'
  
  
FROM data_siswa
LEFT JOIN data_absen ON data_siswa.s_uid = data_absen.uid;


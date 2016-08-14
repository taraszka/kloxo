<?php 

include_once "lib/html/displayinclude.php";

schedulebackup_main();


function schedulebackup_main()
{
	global $gbl, $sgbl, $login, $ghtml; 

//	$progname = $sgbl->__var_program_name;
	$progname = 'kloxomr70';

	initProgram('admin');

	$login->loadAllBackups();
	$list = $login->lxbackup_l;

	foreach($list as $l) {
		$l->backupstage = 'done';
		$l->setUpdateSubaction();
		$l->write();

		if (($l->parent_clname !== $login->getClName()) && !$l->priv->isOn('backupschedule_flag')) {
			continue;
		}

		if ($l->getParentClass() === 'domain') {
			continue;
		}


		if (!$l->backupschedule_type) {
			continue;
		}

		if ($l->backupschedule_type === 'disabled') {
			continue;
		}

		if ($l->backupschedule_type === 'weekly' && (date('D') !== 'Sun')) {
			continue;
		}

		if ($l->backupschedule_type === 'monthly' && (date('d') !== '01')) {
			continue;
		}

		if ($l->backupschedule_time) {
			$h1 = intval($l->backupschedule_time);
			$h2 = intval(date('G'));
			if (h1 != h2) {
				continue;
			} else {
			/*
				// MR -- it's because scavenge in every 5 miutes
				if (intval(date('i')) > 9) {
					continue;
				}
			*/
			}
		} else {
			if ($name === 'admin') {
				if ($h2 != 0) {
					continue;
				}
			} else {
				if ($h2 != 13) {
					continue;
				}
			}
		/*
			// MR -- it's because scavenge in every 5 miutes
			if (intval(date('i')) > 9) {
				continue;
			}
		*/
		}

	/*
		try {
			$param['backup_to_file_f'] = "{$progname}-scheduled";
			$param['upload_to_ftp'] = $l->upload_to_ftp;
			$backup = $l;
			$object = $l->getParentO();
			$backup->doUpdateBackup($param);
			$backup->backupstage = 'done';
		} catch (exception $e) {
			$mess = "{$e->__full_message}\n";
			$backup->backupstage = "Failed due to: {$mess}";

			lx_mail($progname, $object->contactemail, "Backup Failed..", "Backup Failed for {$object->nname} with the Message {$mess}");
		}
	*/
		$class = $l->getParentClass();
		$name = $l->getParentName();
		$fname = "{$progname}-scheduled";

		print("Scheduling for {$class} {$name}\n");
		lxshell_return("$sgbl->__path_php_path", "../bin/common/backup.php", "--class={$class}", "--name={$name}", "--v-backup_file_name={$fname}");

	}
}

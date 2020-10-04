#!/bin/bash
# Léa
# 28/09/2020
# Script de backup de fichiers web


# date et heure du jour
date=$(date +%Y%m%d_%H%M%S)

target_path="${1}"
target_dir="$(echo ${1} | sed 's/.*\///')"

backup_path="/srv/backup/"
backup_file="${backup_path}${target_dir}_${date}"


function backup {
	tar -czf ${backup_file}.tar.gz -P ${target_path}
}

nb_backup=$(ls $backup_path | wc -l)
echo "${nb_backup}"
if [[ "${nb_backup}" -lt 7 ]] 
then
        echo "sauvegarde en cours de $(echo ${target_dir})"
				backup
else
        echo "suppression du plus vieux fichier de sauvegarde"
        older_file=$(ls ${backup_path} -t | tail -1)
        rm ${backup_path}${older_file}
        echo "sauvegarde de $(echo ${target_dir})"
				backup
fi

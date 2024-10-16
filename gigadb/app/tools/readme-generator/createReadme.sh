#!/usr/bin/env bash
#
# Create readme file and optionally upload to Wasabi

# Stop script upon error
#set -e

PATH=/usr/local/bin:$PATH
export PATH

# Allow all scripts to base themselves from directory where backup script 
# is located
APP_SOURCE=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# Location where readme files will be created
SOURCE_PATH="${APP_SOURCE}/runtime/curators"

# Locations of rclone.conf
BASTION_RCLONE_CONF_LOCATION='/home/centos/.config/rclone/rclone.conf'
DEV_RCLONE_CONF_LOCATION='../wasabi-migration/config/rclone.conf'

# Wasabi directory paths
WASABI_DEV_DIRECTORY="wasabi:gigadb-datasets/dev/pub/10.5524"
WASABI_STAGING_DIRECTORY="wasabi:gigadb-datasets/staging/pub/10.5524"
WASABI_LIVE_DIRECTORY="wasabi:gigadb-datasets/live/pub/10.5524"

# Rclone copy is executed in dry run mode as default. Use --apply flag to turn 
# off dry run mode
dry_run=true

# By default, readme files will be copied into dev directory. Use 
# --use-live-data flag to copy readme files to live directory
use_live_data=false

# Default number of DOIs to process
batch=0

# Parse command line parameters
while [[ $# -gt 0 ]]; do
    case "$1" in
    --doi)
        doi=$2
        shift
        ;;
    --batch)
        batch=$2
        shift
        ;;
    --outdir)
        outdir=$2
        shift
        ;;
    --wasabi)
        wasabi_upload=true
        ;;
    --apply)
        dry_run=false
        ;;
    --use-live-data)
        use_live_data=true
        ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

#######################################
# Set up logging
# Globals:
#   LOGDIR
#   APP_SOURCE
#   doi
# Arguments:
#   None
#######################################
function set_up_logging() {
  LOGDIR="$APP_SOURCE/uploadDir"
  LOGFILE="$LOGDIR/readme_${doi}_$(date +'%Y%m%d_%H%M%S').log"
  mkdir -p "${LOGDIR}"
  touch "${LOGFILE}"
}

#######################################
# Determine path to which directory in bucket readme file should be copied into
# Globals:
#   SOURCE_PATH
#   APP_SOURCE
#   destination_path
#   use_live_data
# Arguments:
#   None
#######################################
function determine_destination_path() {
  # Default is to copy readme file to dev directory because
  # developers have access to the dev bucket directory
  destination_path="${WASABI_DEV_DIRECTORY}"
  # If we are on bastion server then migration user credentials 
  # for wasabi will be used, therefore readme files will be
  # copied to staging directory in bucket
  if [[ $(uname -n) =~ compute ]];then
    destination_path="${WASABI_STAGING_DIRECTORY}"
  fi
  # But if --use-live-data flag is present then always copy to 
  # live directory regardless of environment
  if [ "${use_live_data}" = true ]; then
    echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Copying readme file into LIVE data directory" >> "$LOGFILE"
    destination_path="${WASABI_LIVE_DIRECTORY}"
  fi
}

#######################################
# Determine DOI range directory name
# Globals:
#   dir_range
#   doi
# Arguments:
#   None
#######################################
function get_doi_directory_range() {
  if [ "${doi}" -le 101000 ]; then
    dir_range="100001_101000"
  elif [ "${doi}" -le 102000 ] && [ "${doi}" -ge 101001 ]; then
    dir_range="101001_102000"
  elif [ "${doi}" -le 103000 ] && [ "${doi}" -ge 102001 ]; then
    dir_range="102001_103000"
  fi
}

#######################################
# Copies readme text file into Wasabi bucket
# Globals:
#   source_dataset_path
#   doi_directory
#   SOURCE_PATH
#   doi
#   destination_path
#   dir_range
#   LOGFILE
#   rclone_exit_code
# Arguments:
#   None
#######################################
function copy_to_wasabi() {
  # Create directory path to readme file
  readme_file=""
  if [[ $(uname -n) =~ compute ]];then
    readme_file="/home/centos/readmeFiles/readme_${doi}.txt"
  else
    readme_file="${SOURCE_PATH}/readme_${doi}.txt"
  fi
  doi_directory="${destination_path}/${dir_range}/${doi}/"
  
  # Check readme file exists
  if [ -f "$readme_file" ]; then
    # Continue running script if there is an error executing rclone copy
    set +e
    # Construct rclone command to copy readme file to Wasabi
    rclone_cmd="rclone copy --s3-no-check-bucket ${readme_file} ${doi_directory}"
    if [[ $(uname -n) =~ compute ]];then
      rclone_cmd+=" --config ${BASTION_RCLONE_CONF_LOCATION}"
    else
      rclone_cmd+=" --config ${DEV_RCLONE_CONF_LOCATION}"
    fi
    
    if [ "${dry_run}" = true ]; then
      rclone_cmd+=" --dry-run"
    fi
    rclone_cmd+=" --log-file ${LOGFILE}"
    rclone_cmd+=" --log-level INFO"
    rclone_cmd+=" --stats-log-level DEBUG"
    rclone_cmd+=" >> ${LOGFILE}"
    # Execute command
    eval "${rclone_cmd}"
    # Get exit code for rclone command
    rclone_exit_code=$?
    echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Executed: ${rclone_cmd}" >> "$LOGFILE"
    if [ ${rclone_exit_code} -eq 0 ]; then
      echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully copied file to Wasabi for DOI: $doi" >> "${LOGFILE}"
    else 
      echo "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with copying file to Wasabi - rclone has exit code: ${rclone_exit_code}" >> "${LOGFILE}"
    fi
  else
    echo "$(date +'%Y/%m/%d %H:%M:%S') DEBUG  : Could not find file ${readme_file}" >> "${LOGFILE}"
  fi
}

#######################################
# Main program
# Globals:
#   wasabi_upload
#   dir_range
# Arguments:
#   None
#######################################
function main {
  set_up_logging
  
  count=0
  # Execute loop if number of readme files created is less than batch number
  # or run loop if batch = 0
  while [ "${count}" -lt "${batch}" ] || [ "${batch}" -eq 0 ]; do
    # Conditional for how to generate readme file - dependant on user's environment
    if [[ $(uname -n) =~ compute ]];then
      . /home/centos/.bash_profile
      docker run --rm -v /home/centos/readmeFiles:/app/readmeFiles registry.gitlab.com/$GITLAB_PROJECT/production_tool:$GIGADB_ENV /app/yii readme/create --doi "${doi}" --outdir "${outdir}"
    else
      docker-compose run --rm tool /app/yii readme/create --doi "${doi}" --outdir "${outdir}"
    fi
    exitCode=$?
    if [ "${exitCode}" -eq 74 ]; then
      echo "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Could not save readme file for DOI ${doi} at ${outdir}" >> "$LOGFILE"
      exit 1
    elif [ "${exitCode}" -eq 65 ]; then
      echo "$(date +'%Y/%m/%d %H:%M:%S') WARN  : No dataset for DOI ${doi}" >> "$LOGFILE"
      # Exit if not running in batch mode
      if [ "${batch}" -eq 0 ]; then
        exit 0
      fi
    else
      echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Created readme file for DOI ${doi} in ${SOURCE_PATH}/readme_${doi}.txt" >> "$LOGFILE"

      # Readme file can be copied into Wasabi if --wasabi flag is present
      if [ "${wasabi_upload}" ]; then
        determine_destination_path
        dir_range=""
        get_doi_directory_range
        copy_to_wasabi
      fi

      # Exit if not running in batch mode
      if [ "${batch}" -eq 0 ]; then
        exit 0
      fi
      count=$((count+1))
    fi

    doi=$((doi+1))
  done
}

# Call main function
main "$@"

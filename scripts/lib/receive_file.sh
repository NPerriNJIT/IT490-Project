#!/bin/bash

# Get variables from PHP script arguments
REMOTE_USER=$1
REMOTE_HOST=$2
REMOTE_FILE_PATH=$3
LOCAL_DESTINATION="/tmp/"

# Use sftp command download the file
sftp "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_FILE_PATH}" <<EOF
get "${REMOTE_FILE_PATH}" "${LOCAL_DESTINATION}"
bye
EOF

# Check if file was downloaded successfully
# TODO: Determine whether or not file needs to be extracted on deployment
# If not, make sure its still added on other project machines
if [ $? -eq 0 ]; then
  echo "File downloaded successfully"

  # Extract tar file if it has a .tar extension
  if [[ "${REMOTE_FILE_PATH}" == *.tar ]]; then
    tar -xf "${LOCAL_DESTINATION}/$(basename "${REMOTE_FILE_PATH}")" -C "${LOCAL_DESTINATION}"
    if [ $? -eq 0 ]; then
      echo "File extracted successfully"
      # TODO: Move file to local storage location, tbd where this will be
    else
      echo "Failed to extract file."
      exit 1
    fi
  fi

else
  echo "Failed to download file."
  exit 1
fi

exit 0
#!/bin/bash
# Tools to set up various things after the initial svn checkout

CIVVIC_DIR=$(dirname $(dirname $(readlink -f $0)))
todos=()

# Copy .htaccess.sample files to their respective .htaccess files
for sample_file in `find $CIVVIC_DIR/cakephp/ | grep "\\.sample$"`; do
    sample_path=`dirname $sample_file`
    trunc_name=`basename $sample_file .sample`
    trunc_file=$sample_path/$trunc_name
    if [ ! -f $trunc_file ]
    then
        cp $sample_file $trunc_file
        echo "Copied $sample_file to $trunc_file"
        todos+=("Edit and customize $trunc_file")
    fi
done

if [ ${#todos[@]} -gt 0 ]
    then
    echo ""
    echo "******************* TODO ITEMS *******************"
    for todo in "${todos[@]}"; do
        echo $todo
    done
fi


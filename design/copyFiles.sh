#!/bin/bash

# Define the source and destination directories
sourceDir="dist/assets"
destinationDir="../Resources/Public"

# Ensure the necessary destination subdirectories exist
mkdir -p "$destinationDir/Css"
mkdir -p "$destinationDir/JavaScript"

# Get the CSS and JS files from the source directory
cssFiles=( "$sourceDir"/index-*.css )
jsFiles=( "$sourceDir"/index-*.js )

# Get all .woff, .woff2, and .ttf files from the source directory
fontFiles=( "$sourceDir"/*.woff "$sourceDir"/*.woff2 "$sourceDir"/*.ttf )

# Check if at least one CSS and one JS file exist
if [ -e "${cssFiles[0]}" ] && [ -e "${jsFiles[0]}" ]; then

    # Copy all js files
    cp "$sourceDir"/*.js "$destinationDir"/JavaScript/

    # Define the new file names
    newCssFileName="index-kroenerdigital.css"
    newJsFileName="index-kroenerdigital.js"

    # Define the destination file paths for CSS and JS
    newCssFilePath="${destinationDir}/Css/${newCssFileName}"
    newJsFilePath="${destinationDir}/JavaScript/${newJsFileName}"

    # Copy and rename the CSS file
    cp "${cssFiles[0]}" "$newCssFilePath"
    # Perform the replacement in the new CSS file
    sed -i 's|url(\/assets\/|url(|g' "$newCssFilePath"

    # Copy and rename the JS file
    cp "${jsFiles[0]}" "$newJsFilePath"
    sed -i 's/export{[^}]*};//' "$newJsFilePath"


    # Loop through each font file and copy it to the destination directory
    for fontFile in "${fontFiles[@]}"; do
        # Check if the glob actually found a file
        if [ -e "$fontFile" ]; then
            cp "$fontFile" "${destinationDir}/Css/"
        fi
    done

    # Optionally, copy all *.svg files to the destination directory
    # Uncomment the following line if needed
    # cp "$sourceDir"/*.svg "${destinationDir}/Css/"

    echo "Files copied and renamed successfully."
else
    echo "CSS or JS file not found in the source directory."
    exit 1
fi

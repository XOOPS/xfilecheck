# xfilecheck

## File integrity checker for XOOPS 2.5

This script allows you to check that the XOOPS system files have been correctly uploaded to your server. It reads all the XOOPS files and reports missing or invalid ones.

## Download

- Visit the [xfilecheck Releases area on GitHub](https://github.com/XOOPS/xfilecheck/releases)
- Locate the Checksum verification for XOOPS archive that matches your version of XOOPS
- From the Assets section, choose the .zip or .tar.gz *source code* archive as appropriate to your system

## Checking Files

### Traditional method instructions
- Upload the checksum.php script and checksum.md5 from the downloaded archive file to your XOOPS documents root
- Access http://(yourxoopssite)/checksum.php using your browser
- Correct any issues reported by re-uploading missing or invalid files
- you should remove checksum.php and checksum.md5 after you have verified the files

### Advanced command line usage
- unarchive the xfilecheck archive on your server
- invoke command using: *php checksum.php --root=/file/path/to/XoopsCore25/htdocs*
- Correct any issues reported by re-uploading missing or invalid files

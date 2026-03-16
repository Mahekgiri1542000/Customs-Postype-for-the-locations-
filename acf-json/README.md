# ACF Local JSON

This folder is used by ACF Pro Local JSON.

## How files get created
ACF writes JSON files here automatically when field groups are saved in wp-admin.

## Current project groups expected
- group_loc_v3
- group_loc_v1
- group_svc_v1
- group_prj_v1
- group_com_v1

## Generate/update JSON files
1. Open wp-admin.
2. Go to Custom Fields.
3. Open each group above.
4. Click Update.
5. Confirm JSON files appear in this folder.

## Notes
- Do not add hand-written fake group JSON files.
- Commit generated JSON files to version control.

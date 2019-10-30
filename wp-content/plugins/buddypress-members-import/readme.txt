Before import user follow the below steps:
- Check whether the `assets` directory present in your site/project root directory or not.
- If present then, provide this directory to write permission.
- If not present then, create the same and provide this directory to write permission.

Important Note for Multivalued data:
- Say you have a field "Looking For" and this field is multivalued field(checkbox or select)
- Your options are Man, Woman, Both, Other
- Now for a user want to import Woman then in CSV file under "Looking For" column you have to write "Woman::"
- If for a user want to import Woman and Man then in CSV file under "Looking For" column you have to write "Woman::Man"
- If for a user want to import Woman, Man and both then in CSV file under "Looking For" column you have to write "Woman::Man::Both"

Multivalued field for exiting users:
If you are going to update an existing user data but don't want to update that user's a data which is multivalued field 
then you have to remove that field from the CSV file.

Change Log:
== 4.5 ==
- Added support for Paid Memberships Pro
- Fixed few PHP warning.
- Updated Admin section.
== 4.4 ==
- Increased security.
- Added UTF8 encoding enable/disable option.
== 4.3 ==
- Modified memory_limit value set feature.
- Allow encrypted password impord. It means you can import users from one WP setup to another with out changing their password.
- Compatible with BuddyPress xProfile fields of type image and file.
- Fixed PHP notifications.
- Fixed Friends Mapping.
== 4.2 ==
- Added BuddyPress `member_type` support. Column name should be `bp_member_types`
- Compatible with BuddyPress Members Export plugin.
- Modified sample CSv file.
== 4.1 ==
- Added `member_friends_login` new CSV file column. Now you can do friends mapping using member user_login this column. If that user_login user not present but present in CSV file then after creating that member friend mapping will be done.
- Compatible with BuddyPress Members Export plugin.
== 4.0 ==
- `member_groups_name` new CSV file column created. No more need to create Groups manually.
Just provide the name of the member group plugin will check if the group is not present then it will create and map the member. If group present then get the id of it and map with member. If you want to mapp with multiple groups then separate groups name by ::
- Compatible with BuddyPress Members Export plugin.
== 3.9.1 ==
- Added BP friendship mapping feature.
- Fixed Email send password blank issue.
- Tested with WordPress 4.5 and BuddPress 2.5.1
== 3.9 ==
- User getting fatal error if BuddyPress not installed - Fixed.
== 3.7 ==
- Used WP function to check file type.
- Added support for Membership plug-in. Now subscription can also be mapped through CSV file to a member.
- Added new section to show WP, BP and Membership plug-in fields that needs to be add in CSV file.
- Did some text changes.
- Did the fixes that css file were not loading.
- Added nonce security field.
- Handled blank rows.
== 3.6 ==
- If image/avatar url has space then getting fatal error. Fixed.
- If any image/avatar url is 404 then fatal error coming and stop working. Handled.
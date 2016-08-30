# Changelog of LegionBoard Heart

## 0.2.0 (Upcoming)

This is the second big release of LegionBoard Heart. With changes in
almost all files, ~1.5k additions and ~700 deletions, this is a great
release with many new features:

* Add resource courses (1105fcea)
* Log every activity that affects a resource (ab4411c7)
* Archive courses and teachers (3d206eba, 60797dc5)
* Split up startBy/endBy (33e28deb)
* Fix wrong error messages for 1300 and 1400 (00b138ad)
* Split up version header (2e2a200f)
* Do not generate teacher "All" (fddf07b7)
* New authentication group for admins: % (85e7c3b7)
* New authentication group for seeing times (added, edited) (327ffc11)
* Save username with hash of authentication key (df32d0a3)
* Improve code readability and maintainability
* Separate endpoints from API (6efa5c89)
* Revise documentation (21fc9d0f)
* Use [code climate](https://codeclimate.com/github/legionboard/heart)
* Many more fixes

## 0.2.0-beta3 (30.08.2016)

This is the third beta release of Heart 0.2.0. Here are the changes to
the previous beta release:

* Field `archived` of courses and teachers is now served as a boolean

## 0.2.0-beta2 (26.08.2016)

This is the second beta release of Heart 0.2.0. If you upgrade from
0.2.0-beta1, you have to execute this SQL command:
```
UPDATE [table] SET course = 0 WHERE course IS NULL;
```

* Improved upgrading from 0.1.x to 0.2+

## 0.2.0-beta1 (03.08.2016)

This is the first beta release of Heart 0.2.0.

## 0.1.2 (03.06.2016)

* Let user only configure table prefix instead of every table name

## 0.1.1 (19.04.2016)

* Improve key creation tool
* Faster redirect to website
* Test code with GitLab CI

## 0.1.0 (16.03.2016)

* Initial Release

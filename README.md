Show License
============

This plugin adds the ability to the WoltLab Community Framework to show license information during the installation of packages.

Usage
-----

You have to require the package «com.woltlab.community.roul.pip.showlicense» in the package.xml. For each language you have to insert the following into the instructions-block in the package.xml:
```xml
<licensetexts languagecode="en">license_en.txt</licensetexts>
```
You have to replace 'en' with the languagecode of the specific language and license_en.txt with the name of your license file (it must be a normal text file).

You can define a language as standard that will be shown, if the language the user uses is not available. Simpla add the attribute default="1" to the specific licensetexts-directive. Example:
```xml
<licensetexts languagecode="en" default="1">license_en.txt</licensetexts>
```
Otherwise English will be used as standard if available. If you don't set a standard and English is not available or both languages are not installed, the first available installed language will be used. If no available language is installed the plugin will cancel the installation. The license files have to be directly in the package archive with the package.xml.

License
-------

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


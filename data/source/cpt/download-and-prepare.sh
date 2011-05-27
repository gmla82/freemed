#!/bin/bash
#
# $Id$
#
# Authors:
#      Jeff Buchbinder <jeff@freemedsoftware.org>
#
# FreeMED Electronic Medical Record and Practice Management System
# Copyright (C) 1999-2011 FreeMED Software Foundation
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

RVU_FILE=RVU11A.zip
HCPCS_FILE=11anweb.zip
CURDIR="$( pwd )"

cd "$( cd "$(dirname "$0")"; pwd )"

# Retrieve original file
wget -c "http://www.cms.hhs.gov/physicianfeesched/downloads/$RVU_FILE" -O "$RVU_FILE"
wget -c "http://www.cms.hhs.gov/HCPCSReleaseCodeSets/Downloads/${HCPCS_FILE}?agree=yes&next=Accept" -O "${HCPCS_FILE}"

# Extract all files from archive
rm -rf extract
mkdir extract
( \
	cd extract ; \
	unzip ../$RVU_FILE ; \
	unzip ../$HCPCS_FILE ; \
	mv PPRRVU*.csv ../PPRRVU.csv \
)
rm extract -Rf

# Restore previous directory
cd "$CURDIR"

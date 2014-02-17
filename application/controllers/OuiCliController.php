<?php

/*
 * Copyright (C) 2009-2014 Internet Neutral Exchange Association Limited.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


/**
 * Controller: OUI CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2014, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class OuiCliController extends IXP_Controller_CliAction
{
    public function updateDatabaseAction()
    {
        $ouitool = new IXP_OUI( $this->getParam( 'fromfile', false ) );

        $ouiRepo = $this->getD2R( '\\Entities\OUI' );

        $this->getD2EM()->getConnection()->beginTransaction();
        
        if( $refresh = $this->getParam( 'refresh', false ) )
            $ouiRepo->clear();

        foreach( $ouitool->loadList()->processRawData() as $oui => $organisation )
        {
            if( !$refresh && ( $o = $ouiRepo->findOneBy( [ 'oui' => $oui ] ) ) )
            {
                if( $o->getOrganisation() != $organisation )
                    $o->setOrganisation( $organisation );
                continue;
            }

            $o = new \Entities\OUI;
            $o->setOui( $oui );
            $o->setOrganisation( $organisation );
            $this->getD2EM()->persist( $o );
        }

        $this->getD2EM()->flush();
    }

}


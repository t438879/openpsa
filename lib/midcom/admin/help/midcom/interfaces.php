<?php
/**
 * @package midcom.admin.help
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * MidCOM online help interface class
 *
 * @package midcom.admin.help
 */
class midcom_admin_help_interface extends midcom_baseclasses_components_interface
{
    public function __construct()
    {
        $this->_autoload_libraries = array
        (
            'net.nehmer.markdown',
        );
    }
}
?>
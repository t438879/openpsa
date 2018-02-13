<?php
/**
 * @package org.openpsa.projects
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Project tasks handler
 *
 * @package org.openpsa.projects
 */
class org_openpsa_projects_handler_task_list_project extends org_openpsa_projects_handler_task_list
{
    protected $show_status_controls = true;

    protected $is_single_project = true;

    protected $show_customer = false;

    /**
     * @param mixed $handler_id The ID of the handler.
     * @param array $args The argument list.
     * @param array &$data The local request data.
     */
    public function _handler_list($handler_id, array $args, array &$data)
    {
        $this->prepare_request_data('project_tasks');
        $this->prepare_toolbar();

        $data['project'] = new org_openpsa_projects_project($args[0]);

        $this->qb = org_openpsa_projects_task_dba::new_query_builder();
        $this->qb->add_constraint('project', '=', $data['project']->id);
        $this->add_filters('project');
    }

    /**
     * @param mixed $handler_id The ID of the handler.
     * @param array $args The argument list.
     * @param array &$data The local request data.
     */
    public function _handler_json($handler_id, array $args, array &$data)
    {
        $project = new org_openpsa_projects_project($args[0]);
        $this->provider = new org_openpsa_widgets_grid_provider($this, 'json');

        $this->qb = org_openpsa_projects_task_dba::new_query_builder();
        $this->qb->add_constraint('project', '=', $project->id);
        $this->qb->add_order('status');
        $this->qb->add_order('end', 'DESC');
        $this->qb->add_order('start');

        midcom::get()->skip_page_style = true;
        $siteconfig = org_openpsa_core_siteconfig::get_instance();
        $data['contacts_url'] = $siteconfig->get_node_full_url('org.openpsa.contacts');
    }

    /**
     *
     * @param mixed $handler_id The ID of the handler.
     * @param array &$data The local request data.
     */
    public function _show_json($handler_id, array &$data)
    {
        $data['provider'] = $this->provider;
        midcom_show_style('show-json-tasks');
    }
}
<ul id="nav-mobile" class="flush-left">
    <li class="inactive "><a href="#" class="submenu-button">Tickets</a>
		<ul class="submenu">
			<li><a class="newTicket sub" href="tickets.php?a=open" title="Open a New Ticket" id="new-ticket">New Ticket</a>
			</li>
			<li><a class="Ticket active sub" href="tickets.php?status=open" title="Open Tickets" id="subnav3">Open (19)</a>
			</li>
			<li><a class="answeredTickets sub" href="tickets.php?status=answered" title="Answered Tickets" id="subnav4">Answered (3)</a>
			</li>
			<li><a class="assignedTickets sub" href="tickets.php?status=assigned" title="Assigned Tickets" id="subnav5">My Tickets (5)</a>
			</li>
			<li><a class="overdueTickets sub" href="tickets.php?status=overdue" title="Stale Tickets" id="subnav6">Overdue (20)</a>
			</li>
			<li><a class="closedTickets sub" href="tickets.php?status=closed" title="Closed Tickets" id="subnav7">Closed</a>
			</li>
        </ul>
    </li>
    <li class="inactive "><a href="#" class="submenu-button">Users</a>
		<ul class="submenu">
            <li><a class="teams" href="users.php" title="" id="nav0">User Directory</a>
            </li>
            <li><a class="departments" href="orgs.php" title="" id="nav1">Organizations</a>
            </li>
        </ul>
    </li>
    <li class="inactive "><a href="#" class="submenu-button">Tareas</a>
		<ul class="submenu">
            <li><a class="Ticket" href="tasks.php" title="" id="nav0">Tareas</a>
            </li>
        </ul>
    </li>

    <li class="inactive "><a href="#" class="submenu-button">Knowledgebase</a>
		<ul class="submenu">
            <li><a class="kb" href="kb.php" title="" id="nav0">FAQs</a>
            </li>
            <li><a class="faq-categories" href="categories.php" title="" id="nav1">Categories</a>
            </li>
            <li><a class="canned" href="canned.php" title="" id="nav2">Canned Responses</a>
            </li>
        </ul>
    </li>
    <li><a href="#" class="submenu-button">Dashboard</a>
		<ul class="submenu">
			<li><a href="dashboard.php">Dashboard</a>
			</li>
			<li><a href="directory.php">Agent Directory</a>
			</li>
			<li><a href="profile.php">My Profile</a>
			</li>
		</ul>	
    </li>
    <li id="welcome"><a href="#" class="submenu-button"><?php echo sprintf(__('Welcome, %s'), '<strong>'.$thisstaff->getFirstName().'</strong>'); ?></a>
		<ul class="submenu">
			<li><a href="profile.php">Your Profile</a>
			</li>
			<li><a href="logout.php?auth=<?php echo $ost->getLinkToken(); ?>" class="no-pjax"><?php echo __('Log Out'); ?></a>
			</li>
		</ul>	
    </li>	
	
    <li id="contact-id"><a href="#" class="submenu-button">Your Company Ltd.</a>
    </li>
</ul>

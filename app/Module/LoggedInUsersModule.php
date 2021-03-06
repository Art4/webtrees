<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LoggedInUsersModule
 */
class LoggedInUsersModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * LoggedInUsersModule constructor.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module. (A list of users who are online now) */
        return I18N::translate('Who is online');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Who is online” module */
        return I18N::translate('A list of users and visitors who are currently online.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $ctype
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $ctype = '', array $cfg = []): string
    {
        $anonymous = 0;
        $logged_in = [];
        $content   = '';
        foreach ($this->user_service->allLoggedIn() as $user) {
            if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
                $logged_in[] = $user;
            } else {
                $anonymous++;
            }
        }
        $count_logged_in = count($logged_in);
        $content .= '<div class="logged_in_count">';
        if ($anonymous) {
            $content .= I18N::plural('%s anonymous signed-in user', '%s anonymous signed-in users', $anonymous, I18N::number($anonymous));
            if ($count_logged_in) {
                $content .= '&nbsp;|&nbsp;';
            }
        }
        if ($count_logged_in) {
            $content .= I18N::plural('%s signed-in user', '%s signed-in users', $count_logged_in, I18N::number($count_logged_in));
        }
        $content .= '</div>';
        $content .= '<div class="logged_in_list">';
        if (Auth::check()) {
            foreach ($logged_in as $user) {
                $individual = Individual::getInstance($tree->getUserPreference($user, 'gedcomid'), $tree);

                $content .= '<div class="logged_in_name">';
                if ($individual) {
                    $content .= '<a href="' . e($individual->url()) . '">' . e($user->realName()) . '</a>';
                } else {
                    $content .= e($user->realName());
                }
                $content .= ' - ' . e($user->userName());
                if (Auth::id() !== $user->id() && $user->getPreference('contactmethod') !== 'none') {
                    $content .= '<a href="' . e(route('message', ['to'  => $user->userName(), 'ged' => $tree->name()])) . '" class="btn btn-link" title="' . I18N::translate('Send a message') . '">' . view('icons/email') . '</a>';
                }
                $content .= '</div>';
            }
        }
        $content .= '</div>';

        if ($anonymous === 0 && $count_logged_in === 0) {
            return '';
        }

        if ($ctype !== '') {
            return view('modules/block-template', [
                'block'      => str_replace('_', '-', $this->name()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $this->title(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /** {@inheritdoc} */
    public function loadAjax(): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function isUserBlock(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isTreeBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param Request $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(Request $request, int $block_id)
    {
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id)
    {
    }
}

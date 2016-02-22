<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Git\Communication\Console;

class GitFlowFinishConsole extends AbstractGitFlowConsole
{

    const COMMAND_NAME = 'gitflow:finish';
    const DESCRIPTION = 'Finish branch';

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
    }

    /**
     * @param string $from
     * @param string $branch
     *
     * @return array
     */
    protected function getCommandList($from, $branch)
    {
        return [
            'git checkout ' . $from,
            'git pull --rebase',
            'git checkout ' . $branch,
            'git rebase ' . $from,
            'git push -f origin ' . $branch,
            'git checkout ' . $from,
            'git merge --no-ff ' . $branch,
            'git push origin ' . $from,
            'git branch -d ' . $branch,
            'git push origin :' . $branch,
        ];
    }

}

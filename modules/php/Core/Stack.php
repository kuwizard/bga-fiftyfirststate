<?php

namespace STATE\Core;

/*
 * Stack: a class that handles resolution stack
 */

use STATE\Managers\Players;

class Stack
{
    public static function setup($flow)
    {
        $ctx = self::getCtx();
        if (empty($ctx)) {
            // Ok, that should be the very game start
            $firstAtom = self::newAtom(ST_NEXT_ROUND);
            self::setCtx($firstAtom);
            $stack = [$firstAtom];
        } else {
            $stack = [$ctx];
        }

        foreach ($flow as $state) {
            $options = [];
            if ($state === ST_PHASE_THREE_ACTION) {
                $options['suspended'] = true;
                $options['pId'] = Globals::getFirstPlayerId();
            }
            $stack[] = self::newAtom($state, $options);
        }
        self::set($stack);
        self::setCtx($stack[0]);
    }

    public static function top()
    {
        $stack = self::get();
        return reset($stack);
    }

    public static function getNextAtom()
    {
        $stack = self::get();
        return $stack[1] ?? null;
    }

    public static function resolve()
    {
        $atom = self::top();
        $activePlayerId = (int) Game::get()->getActivePlayerId();
        // Jump to resolveStack state to ensure we can change active pId
        Game::get()->gamestate->jumpToState(ST_RESOLVE_STACK);
        // We would like to always switch player if state is suspended (it should be phase 3: action)
        if (self::isSuspended($atom)) {
            $nextPId = Players::isAllPassed() ? Players::getFirstPlayerId() : Players::getNextId();
            Game::get()->gamestate->changeActivePlayer($nextPId);
            $activePlayerId = $nextPId;
            Game::get()->undoSavepoint();
        }
        if (isset($atom['pId']) && $activePlayerId !== $atom['pId']) {
            Game::get()->gamestate->changeActivePlayer($atom['pId']);
        }
        // Removing a hack to allow first player to play first in phase 3
        if (isset($atom['pId']) && $atom['state'] === ST_PHASE_THREE_ACTION) {
            $stack = self::get();
            $top = array_splice($stack, 0, 1);
            unset($top[0]['pId']);
            array_splice($stack, 0, 0, $top);
            self::set($stack);
        }
        Game::get()->gamestate->jumpToState($atom['state']);
    }

    /**
     * @param int $state
     * @param array $options
     * @return void
     */
    public static function insertOnTop($state, $options = [])
    {
        $atom = self::newAtom($state, $options);
        $stack = self::get();
        array_unshift($stack, $atom);
        self::set($stack);
        if (Globals::enabledStackLogger()) {
            var_dump('[Stack logger] Inserted a new atom on top and now Stack looks like this:');
            var_dump(self::get());
        }
    }

    /**
     * @param int $state
     * @param array $options
     * @return void
     */
    public static function insertOnTopAndFinish($state, $options = [])
    {
        self::insertOnTop($state, $options);
        self::finishState();
    }

    private static function get()
    {
        return Globals::getStack();
    }

    private static function set($stack)
    {
        Globals::setStack($stack);
    }

    public static function getCtx()
    {
        return Globals::getStackCtx();
    }

    private static function setCtx($ctx)
    {
        Globals::setStackCtx($ctx);
    }

    public static function newAtom($state, $atom = [])
    {
        $atom['state'] = $state;
        $atom = ['uid' => uniqid()] + $atom;
        return $atom;
    }

    /**
     * @param boolean $resolve
     * @param boolean $deleteCtxFromStack
     * @return void
     */
    public static function finishState($resolve = true, $deleteCtxFromStack = true)
    {
        $ctx = self::getCtx();
        if (self::isSuspended($ctx) || !$deleteCtxFromStack) {
            if (Globals::enabledStackLogger()) {
                var_dump('finishState() is called however the ctx atom is suspended or $deleteCtxFromStack is false');
                var_dump('CTX:');
                var_dump($ctx);
            }
        } else {
            $ctxIndex = self::getAtomIndexByUid($ctx['uid']);
            $currentStack = self::get();
            if (Globals::enabledStackLogger()) {
                var_dump('CTX:');
                var_dump($ctx);
                var_dump('INDEX:');
                var_dump($ctxIndex);
            }
            array_splice($currentStack, $ctxIndex, 1);
            if (Globals::enabledStackLogger()) {
                var_dump('NEW STACK:');
                var_dump($currentStack);
            }
            self::set($currentStack);
        }
        if (Globals::enabledStackLogger()) {
            var_dump('FINISHED WITH FINISHING!');
        }

        $atom = self::top();
        if (Globals::enabledStackLogger()) {
            var_dump('[Stack logger] This atom is going to be resolved now:');
            var_dump($atom);
        }
        if (!$atom) {
            throw new \feException('Stack engine is empty !');
        }

        self::setCtx($atom);

        if ($resolve) {
            self::resolve();
        }
    }

    private static function isSuspended($atom)
    {
        return isset($atom['suspended']) && $atom['suspended'];
    }

    public static function unsuspendNext($state = null)
    {
        $atomIndex = self::getFirstSuspendedAtomIndex();
        $stack = self::get();
        $atom = $stack[$atomIndex];
        if ($atom['state'] !== $state) {
            throw new \BgaVisibleSystemException(
                'First suspended atom is not of state ' . $state . ', $atomIndex: ' . $atomIndex
            );
        }

        $atom = array_splice($stack, $atomIndex, 1);
        unset($atom[0]['suspended']);
        array_splice($stack, $atomIndex, 0, $atom);
        self::set($stack);

        if ($stack[$atomIndex]['uid'] == self::getCtx()['uid']) {
            self::setCtx($stack[$atomIndex]);
        }
    }

    private static function getAtomIndexByUid($uid)
    {
        return self::findBy('uid', $uid);
    }

    private static function getFirstAtomIndexByState($state)
    {
        return self::findBy('state', $state);
    }

    private static function getFirstSuspendedAtomIndex()
    {
        return self::findBy('suspended', true);
    }

    public static function isAtomIn(int $state): bool
    {
        $foundAtom = self::findBy('state', $state, false);
        return $foundAtom > -1;
    }

    public static function isSomeAtomsIn(array $states): bool
    {
        foreach ($states as $state) {
            if (self::isAtomIn($state)) {
                return true;
            }
        }
        return false;
    }

    private static function findBy($option, $value, $throwOnError = true)
    {
        $ctxIndex = -1;
        $stack = self::get();
        foreach ($stack as $key => $atom) {
            if (isset($atom[$option]) && $atom[$option] == $value) {
                $ctxIndex = $key;
                break;
            }
        }
        if ($ctxIndex == -1 && $throwOnError) {
            throw new \BgaVisibleSystemException(
                'Class Stack: ctxIndex == -1. Please report this to the BGA bug tracker'
            );
        }
        return $ctxIndex;
    }
}

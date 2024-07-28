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
        $ctx = Stack::getCtx();
        if (empty($ctx)) {
            // Ok, that should be the very game start
            $firstAtom = Stack::newAtom(ST_NEXT_ROUND);
            Stack::setCtx($firstAtom);
            $stack = [$firstAtom];
        } else {
            $stack = [$ctx];
        }

        foreach ($flow as $state) {
            $options = [];
            if ($state === ST_PHASE_THREE_ACTION) {
                $options['suspended'] = true;
            }
            $stack[] = Stack::newAtom($state, $options);
        }
        Stack::set($stack);
        Stack::setCtx($stack[0]);
    }

    public static function top()
    {
        $stack = Stack::get();
        return reset($stack);
    }

    public static function getNextAtom()
    {
        $stack = Stack::get();
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
        }
        Game::get()->gamestate->jumpToState($atom['state']);
    }

    /**
     * @param int $state
     * @param array $options
     * @return array
     */
    public static function insertOnTop($state, $options = [])
    {
        $atom = self::newAtom($state, $options);
        $stack = Stack::get();
        array_unshift($stack, $atom);
        Stack::set($stack);
        if (Globals::enabledStackLogger()) {
            var_dump('[Stack logger] Inserted a new atom on top and now Stack looks like this:');
            var_dump(Stack::get());
        }
        return $atom;
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
        $ctx = Stack::getCtx();
        if (Stack::isSuspended($ctx) || !$deleteCtxFromStack) {
            if (Globals::enabledStackLogger()) {
                var_dump('finishState() is called however the ctx atom is suspended or $deleteCtxFromStack is false');
                var_dump('CTX:');
                var_dump($ctx);
            }
        } else {
            $ctxIndex = Stack::getAtomIndexByUid($ctx['uid']);
            $currentStack = Stack::get();
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
            Stack::set($currentStack);
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

        Stack::setCtx($atom);

        if ($resolve) {
            Stack::resolve();
        }
    }

    private static function isSuspended($atom)
    {
        return isset($atom['suspended']) && $atom['suspended'];
    }

    public static function unsuspendNext($state = null)
    {
        if ($state === null) {
            $atomIndex = Stack::getFirstSuspendedAtomIndex();
        } else {
            $atomIndex = Stack::getFirstAtomIndexByState($state);
        }
        $stack = Stack::get();
        if (Stack::isSuspended($stack[$atomIndex])) {
            $atom = array_splice($stack, $atomIndex, 1);
            unset($atom[0]['suspended']);
            array_splice($stack, $atomIndex, 0, $atom);
            Stack::set($stack);
        }

        if ($stack[$atomIndex]['uid'] == Stack::getCtx()['uid']) {
            Stack::setCtx($stack[$atomIndex]);
        }
    }

    private static function getAtomIndexByUid($uid)
    {
        return Stack::findBy('uid', $uid);
    }

    private static function getFirstAtomIndexByState($state)
    {
        return Stack::findBy('state', $state);
    }

    private static function getFirstSuspendedAtomIndex()
    {
        return Stack::findBy('suspended', true);
    }

    /**
     * @param int $state
     * @return boolean
     */
    public static function isAtomIn($state)
    {
        $foundAtom = self::findBy('state', $state, false);
        return $foundAtom > -1;
    }

    private static function findBy($option, $value, $throwOnError = true)
    {
        $ctxIndex = -1;
        $stack = Stack::get();
        foreach ($stack as $key => $atom) {
            if (isset($atom[$option]) && $atom[$option] == $value) {
                $ctxIndex = $key;
                break;
            }
        }
        if ($ctxIndex == -1 && $throwOnError) {
            debug_print_backtrace();
            throw new \BgaVisibleSystemException(
                'Class Stack: ctxIndex == -1. Please report this to the BGA bug tracker'
            );
        }
        return $ctxIndex;
    }
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WP_Ultimo\Dependencies\Symfony\Component\EventDispatcher;

use WP_Ultimo\Dependencies\Psr\EventDispatcher\StoppableEventInterface;
use WP_Ultimo\Dependencies\Symfony\Component\EventDispatcher\Debug\WrappedListener;
/**
 * The EventDispatcherInterface is the central point of Symfony's event listener system.
 *
 * Listeners are registered on the manager and events are dispatched through the
 * manager.
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Jonathan Wage <jonwage@gmail.com>
 * @author Roman Borschel <roman@code-factory.org>
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Jordan Alliot <jordan.alliot@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    private $listeners = [];
    private $sorted = [];
    private $optimized;
    public function __construct()
    {
        if (__CLASS__ === static::class) {
            $this->optimized = [];
        }
    }
    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event, string $eventName = null) : object
    {
        $eventName = $eventName ?? \get_class($event);
        if (null !== $this->optimized) {
            $listeners = $this->optimized[$eventName] ?? (empty($this->listeners[$eventName]) ? [] : $this->optimizeListeners($eventName));
        } else {
            $listeners = $this->getListeners($eventName);
        }
        if ($listeners) {
            $this->callListeners($listeners, $eventName, $event);
        }
        return $event;
    }
    /**
     * {@inheritdoc}
     */
    public function getListeners(string $eventName = null)
    {
        if (null !== $eventName) {
            if (empty($this->listeners[$eventName])) {
                return [];
            }
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
            return $this->sorted[$eventName];
        }
        foreach ($this->listeners as $eventName => $eventListeners) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }
        return \array_filter($this->sorted);
    }
    /**
     * {@inheritdoc}
     */
    public function getListenerPriority(string $eventName, $listener)
    {
        if (empty($this->listeners[$eventName])) {
            return null;
        }
        if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
            $listener[0] = $listener[0]();
            $listener[1] = $listener[1] ?? '__invoke';
        }
        foreach ($this->listeners[$eventName] as $priority => &$listeners) {
            foreach ($listeners as &$v) {
                if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof \Closure && 2 >= \count($v)) {
                    $v[0] = $v[0]();
                    $v[1] = $v[1] ?? '__invoke';
                }
                if ($v === $listener || $listener instanceof \Closure && $v == $listener) {
                    return $priority;
                }
            }
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function hasListeners(string $eventName = null)
    {
        if (null !== $eventName) {
            return !empty($this->listeners[$eventName]);
        }
        foreach ($this->listeners as $eventListeners) {
            if ($eventListeners) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName], $this->optimized[$eventName]);
    }
    /**
     * {@inheritdoc}
     */
    public function removeListener(string $eventName, $listener)
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }
        if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
            $listener[0] = $listener[0]();
            $listener[1] = $listener[1] ?? '__invoke';
        }
        foreach ($this->listeners[$eventName] as $priority => &$listeners) {
            foreach ($listeners as $k => &$v) {
                if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof \Closure && 2 >= \count($v)) {
                    $v[0] = $v[0]();
                    $v[1] = $v[1] ?? '__invoke';
                }
                if ($v === $listener || $listener instanceof \Closure && $v == $listener) {
                    unset($listeners[$k], $this->sorted[$eventName], $this->optimized[$eventName]);
                }
            }
            if (!$listeners) {
                unset($this->listeners[$eventName][$priority]);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (\is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);
            } elseif (\is_string($params[0])) {
                $this->addListener($eventName, [$subscriber, $params[0]], $params[1] ?? 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, [$subscriber, $listener[0]], $listener[1] ?? 0);
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (\is_array($params) && \is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, [$subscriber, $listener[0]]);
                }
            } else {
                $this->removeListener($eventName, [$subscriber, \is_string($params) ? $params : $params[0]]);
            }
        }
    }
    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners
     * @param string     $eventName The name of the event to dispatch
     * @param object     $event     The event object to pass to the event handlers/listeners
     */
    protected function callListeners(iterable $listeners, string $eventName, object $event)
    {
        $stoppable = $event instanceof StoppableEventInterface;
        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
            $listener($event, $eventName, $this);
        }
    }
    /**
     * Sorts the internal list of listeners for the given event by priority.
     */
    private function sortListeners(string $eventName)
    {
        \krsort($this->listeners[$eventName]);
        $this->sorted[$eventName] = [];
        foreach ($this->listeners[$eventName] as &$listeners) {
            foreach ($listeners as $k => &$listener) {
                if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
                    $listener[0] = $listener[0]();
                    $listener[1] = $listener[1] ?? '__invoke';
                }
                $this->sorted[$eventName][] = $listener;
            }
        }
    }
    /**
     * Optimizes the internal list of listeners for the given event by priority.
     */
    private function optimizeListeners(string $eventName) : array
    {
        \krsort($this->listeners[$eventName]);
        $this->optimized[$eventName] = [];
        foreach ($this->listeners[$eventName] as &$listeners) {
            foreach ($listeners as &$listener) {
                $closure =& $this->optimized[$eventName][];
                if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
                    $closure = static function (...$args) use(&$listener, &$closure) {
                        if ($listener[0] instanceof \Closure) {
                            $listener[0] = $listener[0]();
                            $listener[1] = $listener[1] ?? '__invoke';
                        }
                        ($closure = \Closure::fromCallable($listener))(...$args);
                    };
                } else {
                    $closure = $listener instanceof \Closure || $listener instanceof WrappedListener ? $listener : \Closure::fromCallable($listener);
                }
            }
        }
        return $this->optimized[$eventName];
    }
}

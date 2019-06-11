<?php

namespace CupOfTea\Composer\Normalize;

use Composer\Composer;
use Composer\Script\Event;
use Composer\IO\IOInterface;
use CupOfTea\Package\Package;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\CommandEvent;
use Composer\Script\ScriptEvents;
use Composer\Plugin\PluginInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Composer\EventDispatcher\EventSubscriberInterface;
use CupOfTea\Package\Contracts\Package as PackageContract;

final class AutoNormalizePlugin implements PluginInterface, EventSubscriberInterface, PackageContract
{
    use Package;

    /**
     * Package Name.
     *
     * @const string
     */
    const VENDOR = 'CupOfTea';
    /**
     * Package Name.
     *
     * @const string
     */
    const PACKAGE = 'ComposerAutoNormalize';
    /**
     * Package Version.
     *
     * @const string
     */
    const VERSION = '1.0.0';

    /**
     * The Composer instance.
     *
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * The IO instance.
     *
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * The Input instance.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The Output instance.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            PluginEvents::COMMAND => [
                'fetchOutput',
            ],
            ScriptEvents::POST_INSTALL_CMD => [
                'normalize',
            ],
            ScriptEvents::POST_UPDATE_CMD => [
                'normalize',
            ],
        ];
    }

    /**
     * Apply plugin modifications to Composer
     *
     * @param  Composer  $composer
     * @param  IOInterface  $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Get the Output instance from a CommandEvent.
     *
     * @param  \Composer\Plugin\CommandEvent  $event
     * @return void
     */
    public function fetchOutput(CommandEvent $event)
    {
        $this->output = $event->getOutput();
    }

    /**
     * Normalize the composer.json file.
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     * @throws \Exception
     */
    public function normalize(Event $event)
    {
        if (! $this->composer->getPluginManager()->getGlobalComposer()) {
            return;
        }

        $app = $this->getApplication();
        $input = $this->getInput();
        $output = $this->getOutput();

        $app->resetComposer();
        $app->run($input, $output);
    }

    /**
     * Get the ApplicationCommand instance.
     *
     * @return \Composer\Console\Application
     */
    protected function getApplication()
    {
        global $application;

        return $application;
    }

    /**
     * Get the Input instance for calling the NormalizeCommand.
     *
     * @return \Symfony\Component\Console\Input\ArrayInput
     */
    protected function getInput()
    {
        if (! $this->input) {
            $input = [];
            $options = [];
            $global = $this->composer->getPluginManager()->getGlobalComposer();

            if ($global) {
                $extra = $global->getPackage()->getExtra();

                $input = $extra['auto-normalize'] ?? [];
                $options = $extra['auto-normalize']['options'] ?? [];
            }

            $extra = $this->composer->getPackage()->getExtra();
            $input = array_merge($input, $extra['auto-normalize'] ?? []);
            $options = array_merge($options, $extra['auto-normalize']['options'] ?? []);

            unset($input['options']);

            [$keys, $values] = [array_keys($options), array_values($options)];

            $keys = array_map(function ($key) {
                return '--' . $key;
            }, $keys);

            $options = array_combine($keys, $options);
            $input = array_merge(['command' => 'normalize'], $input, $options);
            $input = array_map(function ($value) {
                if (is_numeric($value)) {
                    return (string) $value;
                }

                return $value;
            }, $input);

            $this->input = new ArrayInput($input);
        }

        return $this->input;
    }

    /**
     * Get the Output instance.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }
}
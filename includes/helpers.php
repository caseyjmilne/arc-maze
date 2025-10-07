<?php

namespace ARC\Maze;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get the Maze instance
 */
function maze() {
    return Maze::getInstance();
}

/**
 * Register a workflow
 */
function register_workflow($name, $workflow) {
    return maze()->registerWorkflow($name, $workflow);
}

/**
 * Get a workflow by name
 */
function get_workflow($name) {
    return maze()->getWorkflow($name);
}

/**
 * Transition an entity from one state to another
 */
function transition($entity, $fromState, $toState, $context = []) {
    return maze()->transition($entity, $fromState, $toState, $context);
}

/**
 * Check if a transition is allowed
 */
function can_transition($entity, $fromState, $toState, $context = []) {
    return maze()->canTransition($entity, $fromState, $toState, $context);
}

/**
 * Get available transitions for a given state
 */
function get_available_transitions($entity, $currentState, $context = []) {
    return maze()->getAvailableTransitions($entity, $currentState, $context);
}

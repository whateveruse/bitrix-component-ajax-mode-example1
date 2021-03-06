<?php

namespace Citrus\Arealty;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\Response\Component;
use Bitrix\Main\Localization\Loc;
use CBitrixComponent;

Loc::loadMessages(__FILE__);

class TemplateComponent extends CBitrixComponent implements Controllerable
{
	/**
	 * @return void
	 */
	public function executeComponent()
	{
		$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		if ($request->isAjaxRequest())
		{
			$ajaxId = $request->get('bxajaxid'); // id компонента который обновляется через ajax
			if ($ajaxId)
			{
				if ($ajaxId != $this->arParams['AJAX_ID'])
				{
					// пропустить обработку компонента
					return;
				}
				/*
				\Bitrix\Main\Diag\Debug::writeToFile([
					'REQUEST_URI' => $_SERVER['REQUEST_URI'],
					'AJAX_MODE' => $this->arParams['AJAX_MODE'],
					'AJAX_ID' => $this->arParams['AJAX_ID'],
				], __METHOD__);
				*/
			}
		}

		$this->includeComponentTemplate();
	}

	public function configureActions()
	{
		return [
			'loadComponent' => [
				'-prefilters' => [
					Authentication::class,
				]
			]
		];
	}

	protected function listKeysSignedParameters()
	{
		return [
			'DATA',
		];
	}

	public function loadComponentAction()
	{
		$arParams = $this->arParams['DATA'];
		$component = $arParams['COMPONENT'];
		unset($arParams['COMPONENT']);
		$template = $arParams['COMPONENT_TEMPLATE'];
		unset($arParams['COMPONENT_TEMPLATE']);

		return new Component($component, $template, $arParams);
	}
}
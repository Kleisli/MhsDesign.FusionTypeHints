<?php

namespace MhsDesign\FusionTypeHints\Fusion;

use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Validation\Validator\ValidatorInterface;
use Neos\Flow\Validation\ValidatorResolver;
use Neos\Fusion\Core\Runtime;
use Neos\Flow\Annotations as Flow;

class TypedRuntime extends Runtime
{

    #[Flow\Inject]
    protected ValidatorResolver $validatorResolver;

    /**
     * Empty override, to avoid injection settings of this 3. party package
     */
    public function injectSettings(array $settings)
    {
    }

    /**
     * Inject settings of the Neos.Fusion package, and let the original runtime handle them.
     * Configured via Objects.yaml
     */
    public function injectFusionSettings(array $settings)
    {
        parent::injectSettings($settings);
    }

    /**
     * A wrapper around Runtime->evaluate for general paths
     * If a relative @ type path exists to the $fusionPath,
     * The library attitude\duck-types-php will then check if the path value complies to the type annotation
     */
    public function evaluate(string $fusionPath, $contextObject = null, string $behaviorIfPathNotFound = self::BEHAVIOR_RETURNNULL)
    {

        $fusionConfiguration = $this->runtimeConfiguration->forPath($fusionPath);
        $pathValue = parent::evaluate($fusionPath, $contextObject, $behaviorIfPathNotFound);
        if (isset($fusionConfiguration['__meta']['validate']) === false) {
            return $pathValue;
        }
        $validateMetaProperty = $fusionConfiguration['__meta']['validate'];

        $validator = $this->getValidator($validateMetaProperty);
        if($validator == null) {
            throw new RuntimeTypeException("Did not find validator '$validateMetaProperty' at $fusionPath",1682772255);
        }
        $validationResult = $validator->validate($pathValue);
        if($validationResult->hasErrors()){
            throw new RuntimeTypeException("Runtime Type checking for '$fusionPath': " . $validationResult->getFirstError()->getMessage(),1641301766);
        }

        return $pathValue;
    }

    /**
     * @param $validateMetaProperty
     * @return ValidatorInterface
     */
    protected function getValidator($validateMetaProperty){
        if(is_string($validateMetaProperty)){
            $validatorString = $validateMetaProperty;
            $validatorOptions = [];
        }else{
            if(is_array($validateMetaProperty)){
                $validatorString = $validateMetaProperty['__value'];
                $validatorOptions = $validateMetaProperty['options'];
            }
        }

        return $this->validatorResolver->createValidator($validatorString, $validatorOptions);

    }

}
